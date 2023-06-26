<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Status;
use App\Models\Tahap;
use App\Traits\CountdownTrait;
use App\Traits\FileTrait;
use App\Traits\TableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EDController extends Controller
{
    use CountdownTrait;
    use FileTrait;
    use TableTrait;

    public function home(Request $request)
    {
        $deadline = $this->Countdown('evaluasi');
        $keterangan = 'Semua data';
        $user = Auth::user();
        $query = Dokumen::where('kategori', 'evaluasi');
        $statuses = Status::all()->take(7);
        $kategori = 'evaluasi';

        if ($user->role_id == 2) {
            $query->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            });
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', $user->user_access_file[0]->jurusan_id)->get();
            $years = Dokumen::where('kategori', 'evaluasi')->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($user->role_id == 3) {
            $evaluasi_diri = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id])->latest('tahun')->first();
            if ($evaluasi_diri->tahun == date('Y')) {
                $id_ed = $evaluasi_diri->id;
                return redirect()->route('ed_table', $id_ed);
            } else {
                $years = ($evaluasi_diri) ? Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id])->latest('tahun')->distinct()->pluck('tahun')->toArray() : null;
                [$id_evaluasi, $sheetData, $file] = null;
                $kategori = 'evaluasi';
                return view('evaluasi_diri.table', compact('deadline', 'id_evaluasi', 'sheetData', 'years', 'file', 'kategori'));
            }
        } else {
            $jurusans = Jurusan::all();
            $prodis = Prodi::all();
            $years = Dokumen::where('kategori', 'evaluasi')->latest('tahun')->distinct()->pluck('tahun')->toArray();
        }

        if ($request->status) {
            $query->where('status_id', $request->status);
        }
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
            $keterangan = $request->tahun;
        }
        if ($request->jurusan) {
            $query->withWhereHas('prodi.jurusan', function ($query) use ($request) {
                $query->where('id', $request->jurusan);
            });
            $keterangan = Jurusan::find($request->jurusan)->nama_jurusan;
        }
        if ($request->prodi) {
            $query->where('prodi_id', $request->prodi);
            $keterangan = Prodi::find($request->prodi)->nama_prodi;
        }

        $data = $query->with('prodi', 'prodi.jurusan', 'status', 'tahap')->latest('updated_at')->paginate(8);
        return view('evaluasi_diri.home', compact('deadline', 'years', 'prodis', 'data', 'jurusans', 'keterangan', 'statuses', 'kategori'));
    }

    public function add_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
            ]);

            $spreadsheet = IOFactory::load($request->file('file'));
            $sheet = $spreadsheet->getSheet(0);
            $columns = ['Standar', 'Kriteria', 'Nilai capaian', 'Sebutan', 'Bobot', 'Nilai Tertimbang', 'Link Bukti'];
            $fileColumns = [];
            for ($column = 'C'; $column <= 'I'; $column++) {
                $cellValue = $sheet->getCell($column . 2)->getValue();
                array_push($fileColumns, $cellValue);
            }

            $missingColumns = array_diff($columns, $fileColumns);
            if (count($missingColumns) > 0) {
                return back()->with('error', 'Mohon periksa kembali file yang Anda unggah! Kolom yang diperlukan tidak ditemukan: ' . join(', ', $missingColumns));
            }

            $data = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $request->prodi, 'tahun' => $request->tahun])->first();
            if ($data) {
                $this->DeleteFile($data->file_data);
                $prodi = $data->prodi;
            } else {
                $prodi = Prodi::find($request->prodi);
            }
            $path = $this->UploadFile($request->file('file'), "Instrumen Simulasi Akreditasi_" . $prodi->nama_prodi . "_" . $request->tahun . ".xlsx");
            $eddata = Dokumen::updateOrCreate(
                ['prodi_id' => $request->prodi, 'tahun' => $request->tahun, 'kategori' => 'evaluasi'],
                [
                    'status_id' => 1,
                    'file_data' => $path,
                ]
            );
            $eddata->touch();
            Tahap::updateOrCreate(['dokumen_id' => $eddata->id, 'status_id' => 1]);
            activity()
                ->performedOn($eddata)
                ->event('Simulasi akreditasi')
                ->log('Menambahkan data ' . basename($eddata->file_data));
            return redirect()->route('ed_home')->with('success', 'File berhasil ditambahkan');
        }
        return back()->with('error', 'File gagal ditambahkan');
    }

    public function table($id_evaluasi)
    {
        $table = $this->EDTable($id_evaluasi);
        $file = $table[0];
        $sheetData = $table[1];
        $user = Auth::user();
        $kategori = 'evaluasi';
        $deadline = $this->Countdown('evaluasi');

        if (($user->role_id == 3 && $file->prodi_id != $user->user_access_file[0]->prodi_id)) {
            activity()
                ->event('Simulasi akreditasi')
                ->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 2 && $file->prodi->jurusan->id != $user->user_access_file[0]->jurusan_id) {
            activity()
                ->event('Simulasi akreditasi')
                ->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

        if ($user->role_id == 3) {
            $years = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $file->prodi_id])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } else {
            $years = null;
        }

        return view('evaluasi_diri.table', compact('deadline', 'id_evaluasi', 'sheetData', 'years', 'file', 'kategori'));
    }

    public function delete($id_evaluasi)
    {
        if ($id_evaluasi) {
            $file = Dokumen::find($id_evaluasi);
            $this->DeleteFile($file->file_data);
            activity()
                ->performedOn($file)
                ->event('Simulasi akreditasi')
                ->log('Menghapus data ' . basename($file->file_data));
            $file->delete();
        } else {
            return back()->with('error', 'Gagal menghapus file');
        }
        return redirect()->route('ed_home')->with('success', 'File berhasil dihapus');
    }

    public function change_action(Request $request)
    {
        $data = Dokumen::find($request->id_evaluasi);
        $prodi = Prodi::find($request->prodi);

        if ($request->prodi != $data->prodi_id) {
            $exist = Dokumen::where(['prodi_id' => $request->prodi, 'tahun' => $request->tahun, 'kategori' => 'evaluasi'])->first();
            if ($exist) {
                return back()->with('error', 'Instrumen simulasi akreditasi ' . $prodi->nama_prodi . ' ' . $request->tahun . ' sudah ada');
            }
        }

        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
            ]);

            $spreadsheet = IOFactory::load($request->file('file'));
            $sheet = $spreadsheet->getSheet(0);
            $columns = ['Standar', 'Kriteria', 'Nilai capaian', 'Sebutan', 'Bobot', 'Nilai Tertimbang', 'Link Bukti'];
            $fileColumns = [];
            for ($column = 'C'; $column <= 'I'; $column++) {
                $cellValue = $sheet->getCell($column . 2)->getValue();
                array_push($fileColumns, $cellValue);
            }

            $missingColumns = array_diff($columns, $fileColumns);
            if (count($missingColumns) > 0) {
                return back()->with('error', 'Mohon periksa kembali file yang Anda unggah! Kolom yang diperlukan tidak ditemukan: ' . join(', ', $missingColumns));
            }

            $this->DeleteFile($data->file_data);
            $path = $this->UploadFile($request->file('file'), "Instrumen Simulasi Akreditasi_" . $prodi->nama_prodi . "_" . $request->tahun . ".xlsx");
        } else {
            $path = "Files/Instrumen Simulasi Akreditasi_" . $prodi->nama_prodi . "_" . $data->tahun . ".xlsx";
            $this->ChangeFileName($data->file_data, $path);
        }

        $data->update([
            'prodi_id' => $request->prodi,
            'status_id' => 1,
            'kategori' => 'evaluasi',
            'file_data' => $path,
        ]);
        $data->touch();
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 1]);

        activity()
            ->performedOn($data)
            ->event('Simulasi akreditasi')
            ->log('Mengubah instrumen simulasi akreditasi dengan id ' . $data->id);
        return redirect()->route('ed_home')->with('success', 'File berhasil diubah');
    }

    public function filter_year($year)
    {
        $user = Auth::user();
        $data = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year])->first();
        return redirect()->route('ed_table', $data->id);
    }

    public function add()
    {
        $deadline = $this->Countdown('evaluasi');
        $kategori = 'evaluasi';
        $prodis = Prodi::where('jurusan_id', Auth::user()->user_access_file[0]->jurusan_id)->get();
        return view('evaluasi_diri.import_form', compact('deadline', 'prodis', 'kategori'));
    }

    public function change($id_evaluasi)
    {
        $deadline = $this->Countdown('evaluasi');
        $kategori = 'evaluasi';
        $prodis = Prodi::where('jurusan_id', Auth::user()->user_access_file[0]->jurusan_id)->get();
        $data = Dokumen::find($id_evaluasi);
        return view('evaluasi_diri.change_form', compact('deadline', 'prodis', 'data', 'kategori'));
    }

    public function export_all(Request $request)
    {
        if ($request->data) {
            $zipname = 'Files/Instrumen Simulasi Akreditasi.zip';
            if (Storage::disk('public')->exists($zipname)) {
                $this->DeleteZip($zipname);
                $this->ExportZip($zipname, $request->data);
            } else {
                $this->ExportZip($zipname, $request->data);
            }
            activity()
                ->event('Simulasi akreditasi')
                ->log('Export instrumen simulasi akreditasi files to zip');
            return response()->download(storage_path('app/public/' . $zipname));
        }
        return back()->with('error', 'Tidak ada data yang dipilih');
    }

    public function export_file(Request $request)
    {
        activity()
            ->event('Simulasi akreditasi')
            ->log('Export instrumen simulasi akreditasi file ' . basename($request->filename));
        return response()->download(storage_path('app/public/' . $request->filename));
    }
}