<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Dokumen;
use App\Models\Prodi;
use App\Models\Tahap;
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
            $data = Dokumen::where('kategori', 'standar')->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->with('prodi', 'status')->latest('tahun')->paginate(8);
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', $user->user_access_file[0]->jurusan_id)->get();
            $years = Dokumen::where('kategori', 'standar')->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($user->role_id == 3) {
            $ketercapaian_standar = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id])->latest('tahun')->first();
            if ($ketercapaian_standar->tahun == date('Y')) {
                $id_ed = $ketercapaian_standar->id;
                return redirect()->route('ks_table', $id_ed);
            } else {
                $years = ($ketercapaian_standar) ? Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id])->latest('tahun')->distinct()->pluck('tahun')->toArray() : null;
                [$id_standar, $sheetData, $headers, $sheetName, $data] = null;
                return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'data'));
            }
        } else {
            $data = Dokumen::where('kategori', 'standar')->with('prodi.jurusan', 'prodi', 'status')->latest('tahun')->paginate(8);
            $jurusans = Jurusan::all();
            $prodis = Prodi::all();
            $years = Dokumen::where('kategori', 'standar')->latest('tahun')->distinct()->pluck('tahun')->toArray();
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

            $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $request->prodi, 'tahun' => $request->tahun])->first();
            if ($data) {
                $this->DeleteFile($data->file_data);
                $prodi = $data->prodi;
            } else {
                $prodi = Prodi::find($request->prodi);
            }
            $extension = $request->file('file')->extension();
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            $ksdata = Dokumen::updateOrCreate(
                ['prodi_id' => $request->prodi, 'tahun' => $request->tahun],
                [
                    'status_id' => 1,
                    'kategori' => 'standar',
                    'file_data' => $path,
                ]
            );
            Tahap::updateOrCreate(['dokumen_id' => $ksdata->id, 'status_id' => 1]);
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
            $file = Dokumen::find($id_standar);
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
        $data = Dokumen::find($id_standar);

        if (($user->role_id == 3 && $data->prodi_id != $user->user_access_file[0]->prodi_id)) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 2 && $data->prodi->jurusan->id != $user->user_access_file[0]->jurusan_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $sheetCount = $file->getSheetCount();
        $sheetName = $file->getSheetNames();
        for ($i = 0; $i < $sheetCount - 2; $i++) {
            $sheet = $file->getSheet($i)->toArray(null, true, true, true);
            $header = array_shift($sheet);

            array_push($sheetData, $sheet);
            array_push($headers, $header);
        }
        $years = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $data->prodi_id])->latest('tahun')->distinct()->pluck('tahun')->toArray();
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
            $prodis = Prodi::where('jurusan_id', $user->user_access_file[0]->jurusan_id)->get();
            $years = Dokumen::where('kategori', 'standar')->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = Dokumen::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->where(['kategori' => 'standar', 'tahun' => $year])->with('prodi', 'status')->latest('tahun')->paginate(8);
        } elseif ($user->role_id == 3) {
            $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year])->first();
            return redirect()->route('ks_table', $data->id);
        } else {
            $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $year])->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
            $prodis = Prodi::all();
            $years = Dokumen::where('kategori', 'standar')->latest('tahun')->distinct()->pluck('tahun')->toArray();

        }
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function filter_prodi($prodi_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->KSCountdown();
        $user = Auth::user();
        if ($user->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', $user->user_access_file[0]->jurusan_id)->get();
            $years = Dokumen::where('kategori', 'standar')->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = Dokumen::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->where(['kategori' => 'standar', 'prodi_id' => $prodi_id])->with('prodi', 'status')->latest('tahun')->paginate(8);
        } else {
            $prodis = Prodi::all();
            $years = Dokumen::where('kategori', 'standar')->latest('tahun')->distinct()->pluck('tahun')->toArray();
            $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $prodi_id])->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
        }
        $keterangan = ($data->count()) ? $data[0]->prodi->nama_prodi : 'Data kosong';
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function filter_jurusan($jurusan_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->KSCountdown();
        $prodis = Prodi::all();
        $years = Dokumen::where('kategori', 'standar')->latest('tahun')->distinct()->pluck('tahun')->toArray();
        $data = Dokumen::where('kategori', 'standar')->withWhereHas('prodi.jurusan', function ($query) use ($jurusan_id) {
            $query->where('id', $jurusan_id);
        })->with('prodi', 'status')->latest('tahun')->paginate(8);
        $keterangan = ($data->count()) ? $data[0]->prodi->jurusan->nama_jurusan : 'Data kosong';
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans', 'keterangan'));
    }

    public function add()
    {
        $deadline = $this->KSCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->user_access_file[0]->jurusan_id)->get();
        return view('ketercapaian_standar.import_form', compact('deadline', 'prodis'));
    }

    public function change($id_standar)
    {
        $deadline = $this->KSCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->user_access_file[0]->jurusan_id)->get();
        $data = Dokumen::find($id_standar);
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

            $data = Dokumen::find($request->id_standar);
            $this->DeleteFile($data->file_data);
            $extension = $request->file('file')->extension();
            $prodi = Prodi::find($request->prodi);
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            Dokumen::updateOrCreate(
                ['id' => $request->id_standar],
                [
                    'prodi_id' => $request->prodi,
                    'status_id' => 1,
                    'kategori' => 'standar',
                    'file_data' => $path,
                ]
            );
            Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 1]);
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
}