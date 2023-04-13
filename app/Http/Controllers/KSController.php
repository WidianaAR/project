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
            })->with('prodi')->get();
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->distinct()->pluck('tahun')->toArray();
        } elseif ($user->role_id == 3) {
            $ketercapaian_standar = KetercapaianStandar::where('prodi_id', $user->prodi_id)->first();
            if ($ketercapaian_standar) {
                $id_ed = $ketercapaian_standar->id;
                return redirect()->route('ks_table', $id_ed);
            } else {
                [$id_standar, $sheetData, $years, $data] = null;
                return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'data'));
            }
        } else {
            $data = KetercapaianStandar::with('prodi.jurusan', 'prodi')->get();
            $jurusans = Jurusan::all();
            $prodis = Prodi::all();
            $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
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

            $data = KetercapaianStandar::where([['prodi_id', '=', $request->prodi], ['tahun', '=', $request->tahun]])->first()->load('prodi');
            if ($data) {
                $this->DeleteFile($data->file_data);
            }
            $extension = $request->file('file')->extension();
            $prodi = $data->prodi;
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            KetercapaianStandar::updateOrCreate(
                ['prodi_id' => $request->prodi, 'tahun' => $request->tahun],
                [
                    'file_data' => $path,
                    'status' => 'ditinjau',
                    'keterangan' => null
                ]
            );
            if (Auth::user()->role_id == 4) {
                return redirect()->route('ks_table', $data->id)->with('success', 'File berhasil diganti');
            }
            return redirect()->route('ks_home')->with('success', 'File berhasil ditambahkan');
        }
        return redirect()->route('ks_home')->with('error', 'File gagal ditambahkan');
    }

    public function delete($id_standar)
    {
        if ($id_standar) {
            $file = KetercapaianStandar::find($id_standar);
            $this->DeleteFile($file->file_data);
            $file->delete();
        }
        return redirect()->route('ks_home')->with('success', 'File berhasil dihapus');
    }

    public function table($id_standar)
    {
        $headers = array();
        $sheetData = array();

        $user = Auth::user();
        $data = KetercapaianStandar::find($id_standar)->load('prodi.jurusan');

        if ($user->role_id == 3 && $data->prodi_id != $user->prodi_id) {
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 2 && $data->prodi->jurusan->id != $user->jurusan_id) {
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
        $years = KetercapaianStandar::where('prodi_id', $data->prodi_id)->distinct()->pluck('tahun')->toArray();
        $deadline = $this->KSCountdown();
        $temuan = (array_key_exists('K', $sheetData[0][0])) ? 'not null' : null;
        return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'data', 'temuan'));
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
            })->distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->where('tahun', '=', $year)->with('prodi')->get();
        } elseif ($user->role_id == 3) {
            $data = KetercapaianStandar::where([['prodi_id', '=', $user->prodi_id], ['tahun', '=', $year]])->first();
            return redirect()->route('ks_table', $data->id);
        } else {
            $data = KetercapaianStandar::where('tahun', $year)->get();
            $prodis = Prodi::all();
            $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();

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
            })->distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->where('prodi_id', '=', $prodi_id)->with('prodi')->get();
        } else {
            $prodis = Prodi::all();
            $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::where('prodi_id', $prodi_id)->with('prodi')->get();
        }
        $keterangan = ($data->count()) ? $data[0]->prodi->nama_prodi : 'Data kosong';
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function filter_jurusan($jurusan_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->KSCountdown();
        $prodis = Prodi::all();
        $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
        $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($jurusan_id) {
            $query->where('id', $jurusan_id);
        })->with('prodi')->get();
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
                    'file_data' => $path
                ]
            );
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
            return response()->download(storage_path('app/public/' . $zipname));
        }
        return back()->with('error', 'Tidak ada file yang dipilih');
    }

    public function export_file(Request $request)
    {
        return response()->download(storage_path('app/public/' . $request->filename));
    }

    public function confirm($id_standar)
    {
        KetercapaianStandar::find($id_standar)->update([
            'status' => 'disetujui',
            'keterangan' => null,
        ]);
        return redirect()->route('ks_home')->with('success', 'Data ketercapaian standar disetujui');
    }

    public function feedback(Request $request)
    {
        $request->validate([
            'id_standar' => 'required',
            'feedback' => 'required',
        ]);
        KetercapaianStandar::find($request->id_standar)->update([
            'keterangan' => $request->feedback,
            'status' => 'perlu perbaikan',
        ]);
        return redirect()->route('ks_table', $request->id_standar)->with('success', 'Feedback berhasil disimpan');
    }

    public function cancel_confirm($id_standar)
    {
        KetercapaianStandar::find($id_standar)->update([
            'status' => 'ditinjau',
            'keterangan' => null,
        ]);
        return redirect()->route('ks_home');
    }
}