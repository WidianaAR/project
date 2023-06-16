<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Dokumen;
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

class KSController extends Controller
{
    use CountdownTrait;
    use FileTrait;
    use TableTrait;

    public function home(Request $request)
    {
        $deadline = $this->Countdown('standar');
        $keterangan = 'Semua data';
        $user = Auth::user();
        $query = Dokumen::where('kategori', 'standar');
        $statuses = Status::all()->take(7);
        $kategori = 'standar';

        if ($user->role_id == 2) {
            $query->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            });
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
                [$id_standar, $sheetData, $headers, $sheetName, $file] = null;
                $kategori = 'standar';
                return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'file', 'kategori'));
            }
        } else {
            $jurusans = Jurusan::all();
            $prodis = Prodi::all();
            $years = Dokumen::where('kategori', 'standar')->latest('tahun')->distinct()->pluck('tahun')->toArray();
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

        $data = $query->with('prodi.jurusan', 'prodi', 'status', 'tahap')->latest('updated_at')->paginate(8);
        return view('ketercapaian_standar.home', compact('deadline', 'years', 'prodis', 'data', 'jurusans', 'keterangan', 'statuses', 'kategori'));
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
            $columns = ['Standar', 'NO', 'PERNYATAAN ISI STANDAR ', 'INDIKATOR ', '', '', 'Satuan'];
            $fileColumns = [];
            for ($column = 'A'; $column <= 'G'; $column++) {
                $cellValue = $sheet->getCell($column . 1)->getValue();
                array_push($fileColumns, $cellValue);
            }
            $missingColumns = array_diff($columns, $fileColumns);
            if (count($missingColumns) > 0) {
                return back()->with('error', 'Mohon periksa kembali file yang Anda unggah! Kolom yang diperlukan tidak ditemukan: ' . join(', ', $missingColumns));
            }

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
                ['prodi_id' => $request->prodi, 'tahun' => $request->tahun, 'kategori' => 'standar'],
                [
                    'status_id' => 1,
                    'file_data' => $path,
                ]
            );
            $ksdata->touch();
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
        } else {
            return back()->with('error', 'Gagal menghapus file');
        }
        return redirect()->route('ks_home')->with('success', 'File berhasil dihapus');
    }

    public function table($id_standar)
    {
        $table = $this->KSTable($id_standar);
        $file = $table[0];
        $headers = $table[1];
        $sheetCount = $table[2];
        $sheetName = $table[3];
        $sheetData = $table[4];
        $user = Auth::user();
        $years = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $file->prodi_id])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        $kategori = 'standar';
        $deadline = $this->Countdown('standar');

        if (($user->role_id == 3 && $file->prodi_id != $user->user_access_file[0]->prodi_id)) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 2 && $file->prodi->jurusan->id != $user->user_access_file[0]->jurusan_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }
        return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'file', 'kategori'));
    }

    public function filter_year($year)
    {
        $user = Auth::user();
        $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year])->first();
        return redirect()->route('ks_table', $data->id);
    }

    public function add()
    {
        $deadline = $this->Countdown('standar');
        $kategori = 'standar';
        $prodis = Prodi::where('jurusan_id', Auth::user()->user_access_file[0]->jurusan_id)->get();
        return view('ketercapaian_standar.import_form', compact('deadline', 'prodis', 'kategori'));
    }

    public function change($id_standar)
    {
        $deadline = $this->Countdown('standar');
        $kategori = 'standar';
        $prodis = Prodi::where('jurusan_id', Auth::user()->user_access_file[0]->jurusan_id)->get();
        $data = Dokumen::find($id_standar);
        return view('ketercapaian_standar.change_form', compact('deadline', 'prodis', 'data', 'kategori'));
    }

    public function change_action(Request $request)
    {
        $data = Dokumen::find($request->id_standar);
        $prodi = Prodi::find($request->prodi);

        if ($request->prodi != $data->prodi_id) {
            $exist = Dokumen::where(['prodi_id' => $request->prodi, 'tahun' => $request->tahun, 'kategori' => 'standar'])->first();
            if ($exist) {
                return back()->with('error', 'File ketercapaian standar ' . $prodi->nama_prodi . ' ' . $request->tahun . ' sudah ada');
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
            $columns = ['Standar', 'NO', 'PERNYATAAN ISI STANDAR ', 'INDIKATOR ', '', '', 'Satuan'];
            $fileColumns = [];
            for ($column = 'A'; $column <= 'G'; $column++) {
                $cellValue = $sheet->getCell($column . 1)->getValue();
                array_push($fileColumns, $cellValue);
            }
            $missingColumns = array_diff($columns, $fileColumns);
            if (count($missingColumns) > 0) {
                return back()->with('error', 'Mohon periksa kembali file yang Anda unggah! Kolom yang diperlukan tidak ditemukan: ' . join(', ', $missingColumns));
            }

            $this->DeleteFile($data->file_data);
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_" . $prodi->nama_prodi . "_" . $request->tahun . ".xlsx");
        } else {
            $path = "Files/Ketercapaian Standar_" . $prodi->nama_prodi . "_" . $data->tahun . ".xlsx";
            $this->ChangeFileName($data->file_data, $path);
        }

        $data->update([
            'prodi_id' => $request->prodi,
            'status_id' => 1,
            'kategori' => 'standar',
            'file_data' => $path,
        ]);
        $data->touch();
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 1]);

        activity()
            ->performedOn($data)
            ->log('Mengubah data ketercapaian standar dengan id ' . $data->id);
        return redirect()->route('ks_home')->with('success', 'File berhasil diubah');
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