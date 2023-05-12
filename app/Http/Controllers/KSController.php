<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\KetercapaianStandar;
use App\Models\Prodi;
use App\Traits\CountdownTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KSController extends Controller
{
    use CountdownTrait;
    use FileTrait;

    public function home()
    {
        $deadline = $this->KSCountdown();
        $keterangan = 'Semua data';
        $user = Auth::user();

        if ($user->role_id == 2) {
            $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->with('prodi')->latest('tahun')->paginate(8);
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($user->role_id == 3 || $user->role_id == 4) {
            $ketercapaian_standar = KetercapaianStandar::where('prodi_id', $user->prodi_id)->latest('tahun')->first();
            if ($ketercapaian_standar->tahun == date('Y')) {
                $id_ed = $ketercapaian_standar->id;
                return redirect()->route('ks_table', $id_ed);
            } else {
                $years = ($ketercapaian_standar) ? KetercapaianStandar::where('prodi_id', $user->prodi_id)->latest('tahun')->distinct()->pluck('tahun')->toArray() : null;
                [$id_standar, $sheetData, $headers, $sheetName, $data] = null;
                return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'data'));
            }
        } else {
            $data = KetercapaianStandar::with('prodi.jurusan', 'prodi')->latest('tahun')->paginate(8);
            $jurusans = Jurusan::all();
            $prodis = Prodi::all();
            $years = KetercapaianStandar::latest('tahun')->distinct()->pluck('tahun')->toArray();
        }
        return view('ketercapaian_standar.home', compact('deadline', 'years', 'prodis', 'data', 'jurusans', 'keterangan'));
    }

    public function add_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                    'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
                ]);

            $data = KetercapaianStandar::where([['prodi_id', '=', $request->prodi], ['tahun', '=', $request->tahun]])->first();
            if ($data) {
                $this->DeleteFile($data->file_data);
                $prodi = $data->prodi;
            } else {
                $prodi = Prodi::find($request->prodi);
            }
            $extension = $request->file('file')->extension();
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            $ksdata = KetercapaianStandar::updateOrCreate(
                ['prodi_id' => $request->prodi, 'tahun' => $request->tahun],
                [
                    'file_data' => $path,
                    'status' => 'ditinjau',
                    'keterangan' => null,
                    'temuan' => null
                ]
            );
            if (Auth::user()->role_id == 4) {
                activity()
                    ->performedOn($ksdata)
                    ->log('Mengubah file ' . basename($ksdata->file_data));
                return redirect()->route('ks_table', $request->id)->with('success', 'File berhasil diganti');
            }
            activity()
                ->performedOn($ksdata)
                ->log('Menambahkan data ' . basename($ksdata->file_data));
            return redirect()->route('ks_home')->with('success', 'File berhasil ditambahkan');
        }
        return redirect()->route('ks_home')->with('error', 'File gagal ditambahkan');
    }

    public function delete($id_standar)
    {
        if ($id_standar) {
            $file = KetercapaianStandar::find($id_standar);
            $this->DeleteFile($file->file_data);
            activity()
                ->performedOn($file)
                ->log('Menghapus data ' . basename($file->file_data));
            $file->delete();
        }
        return redirect()->route('ks_home')->with('success', 'File berhasil dihapus');
    }

    public function table($id_standar)
    {
        $headers = array();
        $sheetData = array();

        $user = Auth::user();
        $data = KetercapaianStandar::find($id_standar);

        if (($user->role_id == 3 && $data->prodi_id != $user->prodi_id) || ($user->role_id == 4 && $data->prodi_id != $user->prodi_id)) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 2 && $data->prodi->jurusan->id != $user->jurusan_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $sheetCount = $file->getSheetCount();
        $sheetName = $file->getSheetNames();
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $file->getSheet($i)->toArray(null, true, true, true);
            $header = array_shift($sheet);

            array_push($sheetData, $sheet);
            array_push($headers, $header);
        }
        $years = KetercapaianStandar::where('prodi_id', $data->prodi_id)->latest('tahun')->distinct()->pluck('tahun')->toArray();
        $deadline = $this->KSCountdown();
        return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'data'));
    }

    public function filter_year($year)
    {
        $deadline = $this->KSCountdown();
        $jurusans = Jurusan::all();
        $keterangan = $year;
        $user = Auth::user();
        if ($user->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->where('tahun', '=', $year)->with('prodi')->latest('tahun')->paginate(8);
        } elseif ($user->role_id == 3 || $user->role_id == 4) {
            $data = KetercapaianStandar::where([['prodi_id', '=', $user->prodi_id], ['tahun', '=', $year]])->first();
            return redirect()->route('ks_table', $data->id);
        } else {
            $data = KetercapaianStandar::where('tahun', $year)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
            $prodis = Prodi::all();
            $years = KetercapaianStandar::latest('tahun')->distinct()->pluck('tahun')->toArray();

        }
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function filter_prodi($prodi_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->KSCountdown();
        $user = Auth::user();
        if ($user->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->where('prodi_id', '=', $prodi_id)->with('prodi')->latest('tahun')->paginate(8);
        } else {
            $prodis = Prodi::all();
            $years = KetercapaianStandar::latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::where('prodi_id', $prodi_id)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
        }
        $keterangan = ($data->count()) ? $data[0]->prodi->nama_prodi : 'Data kosong';
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function filter_jurusan($jurusan_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->KSCountdown();
        $prodis = Prodi::all();
        $years = KetercapaianStandar::latest('tahun')->distinct()->pluck('tahun')->toArray();
        $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($jurusan_id) {
            $query->where('id', $jurusan_id);
        })->with('prodi')->latest('tahun')->paginate(8);
        $keterangan = ($data->count()) ? $data[0]->prodi->jurusan->nama_jurusan : 'Data kosong';
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function add()
    {
        $deadline = $this->KSCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        return view('ketercapaian_standar.import_form', compact('deadline', 'prodis'));
    }

    public function change($id_standar)
    {
        $deadline = $this->KSCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        $data = KetercapaianStandar::find($id_standar);
        return view('ketercapaian_standar.change_form', compact('deadline', 'prodis', 'data'));
    }

    public function change_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                    'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
                ]);

            $data = KetercapaianStandar::find($request->id_standar);
            $this->DeleteFile($data->file_data);
            $extension = $request->file('file')->extension();
            $prodi = Prodi::find($request->prodi);
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            KetercapaianStandar::updateOrCreate(
                ['id' => $request->id_standar],
                [
                    'prodi_id' => $request->prodi,
                    'file_data' => $path,
                    'status' => 'ditinjau',
                    'keterangan' => null,
                    'temuan' => null
                ]
            );
            activity()
                ->performedOn($data)
                ->log('Mengubah data ketercapaian standar dengan id ' . $data->id);
            return redirect()->route('ks_home')->with('success', 'File berhasil diubah');
        }
        return redirect()->route('ks_home')->with('error', 'File gagal diubah');
    }

    public function export_all(Request $request)
    {
        if ($request->data) {
            $zipname = 'Files/Ketercapaian Standar.zip';
            if (Storage::disk('public')->exists($zipname)) {
                $this->DeleteZip($zipname);
                $this->ExportZip($zipname, $request->data);
            } else {
                $this->ExportZip($zipname, $request->data);
            }
            activity()->log('Export ketercapaian standar files to zip');
            return response()->download(storage_path('app/public/' . $zipname));
        }
        return back()->with('error', 'Tidak ada file yang dipilih');
    }

    public function export_file(Request $request)
    {
        activity()->log('Export ketercapaian standar file ' . basename($request->filename));
        return response()->download(storage_path('app/public/' . $request->filename));
    }

    public function confirm($id_standar)
    {
        $data = KetercapaianStandar::find($id_standar);
        $data->update([
            'status' => 'disetujui',
            'keterangan' => null,
        ]);
        activity()
            ->performedOn($data)
            ->log('Konfirmasi ' . basename($data->file_data));
        return redirect()->route('ks_table', $id_standar)->with('success', 'Data ketercapaian standar disetujui');
    }

    public function feedback(Request $request)
    {
        $request->validate([
            'id_standar' => 'required',
            'feedback' => 'required',
        ]);
        $data = KetercapaianStandar::find($request->id_standar);
        $data->update([
            'keterangan' => $request->feedback,
            'status' => 'perlu perbaikan',
        ]);
        activity()
            ->performedOn($data)
            ->log('Memberi koreksi pada file ' . basename($data->file_data));
        return redirect()->route('ks_table', $request->id_standar)->with('success', 'Feedback berhasil disimpan');
    }

    public function cancel_confirm($id_standar)
    {
        $data = KetercapaianStandar::find($id_standar);
        $data->update([
            'status' => 'ditinjau',
            'keterangan' => null,
        ]);
        activity()
            ->performedOn($data)
            ->log('Membatalkan konfirmasi ' . basename($data->file_data));
        return redirect()->route('ks_table', $id_standar);
    }
}