<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiDiri;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Traits\CountdownTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EDController extends Controller
{
    use CountdownTrait;
    use FileTrait;

    public function home()
    {
        $deadline = $this->EDCountdown();
        $keterangan = 'Semua data';
        $user = Auth::user();

        if ($user->role_id == 2) {
            $data = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->with('prodi')->latest('tahun')->paginate(8);
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($user->role_id == 3 || $user->role_id == 4) {
            $evaluasi_diri = EvaluasiDiri::where('prodi_id', $user->prodi_id)->latest('tahun')->first();
            if ($evaluasi_diri->tahun == date('Y')) {
                $id_ed = $evaluasi_diri->id;
                return redirect()->route('ed_table', $id_ed);
            } else {
                $years = ($evaluasi_diri) ? EvaluasiDiri::where('prodi_id', $user->prodi_id)->latest('tahun')->distinct()->pluck('tahun')->toArray() : null;
                [$id_evaluasi, $sheetData, $data] = null;
                return view('evaluasi_diri.table', compact('deadline', 'id_evaluasi', 'sheetData', 'years', 'data'));
            }
        } else {
            $data = EvaluasiDiri::with('prodi.jurusan', 'prodi')->latest('tahun')->paginate(8);
            $jurusans = Jurusan::all();
            $prodis = Prodi::all();
            $years = EvaluasiDiri::latest('tahun')->distinct()->pluck('tahun')->toArray();
        }
        return view('evaluasi_diri.home', compact('deadline', 'years', 'prodis', 'data', 'jurusans', 'keterangan'));
    }

    public function add_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                    'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
                ]);

            $data = EvaluasiDiri::where([['prodi_id', '=', $request->prodi], ['tahun', '=', $request->tahun]])->first();
            if ($data) {
                $this->DeleteFile($data->file_data);
                $prodi = $data->prodi;
            } else {
                $prodi = Prodi::find($request->prodi);
            }
            $extension = $request->file('file')->extension();
            $path = $this->UploadFile($request->file('file'), "Evaluasi Diri_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            $eddata = EvaluasiDiri::updateOrCreate(
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
                    ->performedOn($eddata)
                    ->log('Mengubah file ' . basename($eddata->file_data));
                return redirect()->route('ed_table', $request->id)->with('success', 'File berhasil diganti');
            }
            activity()
                ->performedOn($eddata)
                ->log('Menambahkan data ' . basename($eddata->file_data));
            return redirect()->route('ed_home')->with('success', 'File berhasil ditambahkan');
        }
        return redirect()->route('ed_home')->with('error', 'File gagal ditambahkan');
    }

    public function table($id_evaluasi)
    {
        $data = EvaluasiDiri::find($id_evaluasi);
        $user = Auth::user();

        if (($user->role_id == 3 && $data->prodi_id != $user->prodi_id) || ($user->role_id == 4 && $data->prodi_id != $user->prodi_id)) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 2 && $data->prodi->jurusan->id != $user->jurusan_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);
        $years = EvaluasiDiri::where('prodi_id', $data->prodi_id)->latest('tahun')->distinct()->pluck('tahun')->toArray();
        $deadline = $this->EDCountdown();
        return view('evaluasi_diri.table', compact('deadline', 'id_evaluasi', 'sheetData', 'years', 'data'));
    }

    public function delete($id_evaluasi)
    {
        if ($id_evaluasi) {
            $file = EvaluasiDiri::find($id_evaluasi);
            $this->DeleteFile($file->file_data);
            activity()
                ->performedOn($file)
                ->log('Menghapus data ' . basename($file->file_data));
            $file->delete();
        }
        return redirect()->route('ed_home')->with('success', 'File berhasil dihapus');
    }

    public function change_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                    'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
                ]);

            $data = EvaluasiDiri::find($request->id_evaluasi);
            $this->DeleteFile($data->file_data);
            $extension = $request->file('file')->extension();
            $prodi = Prodi::find($request->prodi);
            $path = $this->UploadFile($request->file('file'), "Evaluasi Diri_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            EvaluasiDiri::updateOrCreate(
                ['id' => $request->id_evaluasi],
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
                ->log('Mengubah data evaluasi diri dengan id ' . $data->id);
            return redirect()->route('ed_home')->with('success', 'File berhasil diubah');
        }
        return redirect()->route('ed_home')->with('error', 'File gagal diubah');
    }

    public function filter_year($year)
    {
        $deadline = $this->EDCountdown();
        $jurusans = Jurusan::all();
        $keterangan = $year;
        $user = Auth::user();
        if ($user->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->where('tahun', '=', $year)->with('prodi')->latest('tahun')->paginate(8);
        } elseif ($user->role_id == 3 || $user->role_id == 4) {
            $data = EvaluasiDiri::where([['prodi_id', '=', $user->prodi_id], ['tahun', '=', $year]])->first();
            return redirect()->route('ed_table', $data->id);
        } else {
            $data = EvaluasiDiri::where('tahun', $year)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
            $prodis = Prodi::all();
            $years = EvaluasiDiri::latest('tahun')->distinct()->pluck('tahun')->toArray();

        }
        return view('evaluasi_diri.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function filter_prodi($prodi_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->EDCountdown();
        $user = Auth::user();
        if ($user->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->where('prodi_id', '=', $prodi_id)->with('prodi')->latest('tahun')->paginate(8);
        } else {
            $prodis = Prodi::all();
            $years = EvaluasiDiri::latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = EvaluasiDiri::where('prodi_id', $prodi_id)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8); //dipersingkat (join dihilangkan) karena menggunakan eloquent relationship
        }
        $keterangan = ($data->count()) ? $data[0]->prodi->nama_prodi : 'Data kosong';
        return view('evaluasi_diri.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function filter_jurusan($jurusan_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->EDCountdown();
        $prodis = Prodi::all();
        $years = EvaluasiDiri::latest('tahun')->distinct()->pluck('tahun')->toArray();
        $data = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($jurusan_id) {
            $query->where('id', $jurusan_id);
        })->with('prodi')->latest('tahun')->paginate(8);
        $keterangan = ($data->count()) ? $data[0]->prodi->jurusan->nama_jurusan : 'Data kosong';
        return view('evaluasi_diri.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function add()
    {
        $deadline = $this->EDCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        return view('evaluasi_diri.import_form', compact('deadline', 'prodis'));
    }

    public function change($id_evaluasi)
    {
        $deadline = $this->EDCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        $data = EvaluasiDiri::find($id_evaluasi);
        return view('evaluasi_diri.change_form', compact('deadline', 'prodis', 'data'));
    }

    public function export_all(Request $request)
    {
        if ($request->data) {
            $zipname = 'Files/Evaluasi Diri.zip';
            if (Storage::disk('public')->exists($zipname)) {
                $this->DeleteZip($zipname);
                $this->ExportZip($zipname, $request->data);
            } else {
                $this->ExportZip($zipname, $request->data);
            }
            activity()->log('Export evaluasi diri files to zip');
            return response()->download(storage_path('app/public/' . $zipname));
        }
        return back()->with('error', 'Tidak ada data yang dipilih');
    }

    public function export_file(Request $request)
    {
        activity()->log('Export evaluasi diri file ' . basename($request->filename));
        return response()->download(storage_path('app/public/' . $request->filename));
    }

    public function confirm($id_evaluasi)
    {
        $data = EvaluasiDiri::find($id_evaluasi);
        $data->update([
            'status' => 'disetujui',
            'keterangan' => null,
        ]);
        activity()
            ->performedOn($data)
            ->log('Konfirmasi ' . basename($data->file_data));
        return redirect()->route('ed_table', $id_evaluasi)->with('success', 'Data evaluasi diri disetujui');
    }

    public function feedback(Request $request)
    {
        $request->validate([
            'id_evaluasi' => 'required',
            'feedback' => 'required',
        ]);
        $data = EvaluasiDiri::find($request->id_evaluasi);
        $data->update([
            'keterangan' => $request->feedback,
            'status' => 'perlu perbaikan',
        ]);
        activity()
            ->performedOn($data)
            ->log('Memberi koreksi pada file ' . basename($data->file_data));
        return redirect()->route('ed_table', $request->id_evaluasi)->with('success', 'Feedback berhasil disimpan');
    }

    public function cancel_confirm($id_evaluasi)
    {
        $data = EvaluasiDiri::find($id_evaluasi);
        $data->update([
            'status' => 'ditinjau',
            'keterangan' => null,
        ]);
        activity()
            ->performedOn($data)
            ->log('Membatalkan konfirmasi ' . basename($data->file_data));
        return redirect()->route('ed_table', $id_evaluasi);
    }
}