<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Tahap;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TilikController extends Controller
{
    use FileTrait;

    public function index_auditor($kategori = null)
    {
        $user = Auth::user()->user_access_file;
        $access_prodi = [];

        foreach ($user as $value) {
            array_push($access_prodi, $value->prodi_id);
        }

        if ($kategori == 'evaluasi') {
            $keterangan = "Evaluasi diri";
            $data = Dokumen::where('kategori', 'evaluasi')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'evaluasi')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($kategori == 'standar') {
            $keterangan = "Ketercapaian standar";
            $data = Dokumen::where('kategori', 'standar')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'standar')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } else {
            $keterangan = "Semua data";
            $data = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        }
        return view('tilik.home_auditor', compact('data', 'years', 'keterangan'));
    }

    public function filter_year_auditor($kategori = null, $year)
    {
        $user = Auth::user()->user_access_file;
        $access_prodi = [];

        foreach ($user as $value) {
            array_push($access_prodi, $value->prodi_id);
        }

        if ($kategori == 'evaluasi') {
            $keterangan = "Evaluasi diri";
            $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $year])->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'evaluasi')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($kategori == 'standar') {
            $keterangan = "Ketercapaian standar";
            $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $year])->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'standar')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } else {
            $keterangan = "Semua data";
            $data = Dokumen::where('tahun', $year)->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        }
        return view('tilik.home_auditor', compact('data', 'years', 'keterangan'));
    }


    public function index($kategori = null)
    {
        $user = Auth::user();
        if ($kategori == 'evaluasi') {
            $keterangan = 'Evaluasi diri';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            }
        } elseif ($kategori == 'standar') {
            $keterangan = 'Ketercapaian standar';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'standar', 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'standar', 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            }
        } else {
            $keterangan = 'Semua data';
            if ($user->role_id == 2) {
                $data = Dokumen::where('status_id', 3)->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where('status_id', 3)->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where('status_id', 3)->with('prodi')->paginate(15);
                $years = Dokumen::where('status_id', 3)->distinct()->pluck('tahun')->toArray();
            }
        }
        return view('tilik.home', compact('data', 'years', 'keterangan'));
    }

    public function filter_year($kategori = null, $year)
    {
        $user = Auth::user();
        if ($kategori == 'evaluasi') {
            $keterangan = 'Evaluasi diri';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $year, 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $year, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            }
        } elseif ($kategori == 'standar') {
            $keterangan = 'Ketercapaian standar';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $year, 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $year, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            }
        } else {
            $keterangan = 'Semua data';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['tahun' => $year, 'status_id' => 3])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where('status_id', 3)->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 3])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['tahun' => $year, 'status_id' => 3])->with('prodi')->paginate(15);
                $years = Dokumen::where('status_id', 3)->distinct()->pluck('tahun')->toArray();
            }
        }
        return view('tilik.home', compact('data', 'years', 'keterangan'));
    }


    public function ed_table($id)
    {
        $data = Dokumen::find($id);
        $user = Auth::user();

        if ($user->role_id == 3 && $data->prodi_id != $user->user_access_file[0]->prodi_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 4) {
            $auditor_prodi = [];
            foreach ($user->user_access_file as $value) {
                array_push($auditor_prodi, $value->prodi_id);
            }
            if (!in_array($data->prodi_id, $auditor_prodi)) {
                activity()->log('Prohibited access | Mencoba akses data prodi lain');
                return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
            }
        } elseif ($user->role_id == 2 && $data->prodi->jurusan->id != $user->user_access_file[0]->jurusan_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id != 4 && $data->status_id != 3) {
            activity()->log('Prohibited access | Mencoba akses data evaluasi diri yang belum memiliki tilik');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);
        return view('tilik.evaluasi', compact('sheetData', 'data'));
    }

    public function ed_table_save(Request $request)
    {
        $tilik = $request->input('tilik');
        $data = Dokumen::find($request->id);
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $worksheet = $file->getSheet(0);

        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);
        $tilikKey = 0;

        $boldCenter = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        foreach ($sheetData as $key => $sheet) {
            if ($sheet[0] == 'No') {
                $worksheet->setCellValue('J' . ($key + 1), 'Tilik');
                $worksheet->getStyle('J' . ($key + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('J' . ($key + 1))->applyFromArray($boldCenter);
            } else {
                if ($sheet[3] && $sheet[1]) {
                    $worksheet->setCellValue('J' . ($key + 1), $tilik[$tilikKey]);
                    $tilikKey++;
                }
            }
        }

        $writer = IOFactory::createWriter($file, 'Xlsx');
        $this->DeleteFile($data->file_data);
        $writer->save(storage_path('app/public/' . $data->file_data));

        $data->update(['status_id' => 3]);
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 3]);
        activity()->log('Menambahkan tilik pada ' . basename($data->file_data));
        return redirect()->route('tilik_ed_table', $data->id)->with('success', 'Tilik berhasil disimpan');
    }


    public function ks_table($id)
    {
        $headers = array();
        $sheetData = array();

        $user = Auth::user();
        $data = Dokumen::find($id);

        if ($user->role_id == 3 && $data->prodi_id != $user->user_access_file[0]->prodi_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 4) {
            $auditor_prodi = [];
            foreach ($user->user_access_file as $value) {
                array_push($auditor_prodi, $value->prodi_id);
            }
            if (!in_array($data->prodi_id, $auditor_prodi)) {
                activity()->log('Prohibited access | Mencoba akses data prodi lain');
                return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
            }
        } elseif ($user->role_id == 2 && $data->prodi->jurusan->id != $user->user_access_file[0]->jurusan_id) {
            activity()->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id != 4 && $data->status_id != 3) {
            activity()->log('Prohibited access | Mencoba akses data ketercapaian standar yang belum memiliki tilik');
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

        return view('tilik.standar', compact('sheetData', 'sheetCount', 'headers', 'sheetName', 'data'));
    }

    public function ks_table_save(Request $request)
    {
        $data = Dokumen::find($request->id);
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $sheetCount = $file->getSheetCount();

        $boldCenter = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $sheetCount = $file->getSheetCount();
        for ($i = 0; $i < $sheetCount - 2; $i++) {
            $worksheet = $file->getSheet($i);
            $rowIndex = 2;

            $worksheet->setCellValue('K1', 'Tilik');
            $worksheet->getStyle('K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
            $worksheet->getStyle('K1')->applyFromArray($boldCenter);

            $tilikData = $request->input($i . 'tilik');
            if (is_array($tilikData)) {
                foreach ($tilikData as $value) {
                    $worksheet->setCellValue('K' . $rowIndex, $value ?? '');
                    $rowIndex++;
                }
            }
        }

        $writer = IOFactory::createWriter($file, 'Xlsx');
        $this->DeleteFile($data->file_data);
        $writer->save(storage_path('app/public/' . $data->file_data));

        $data->update(['status_id' => 3]);
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 3]);
        activity()->log('Menambahkan tilik pada ' . basename($data->file_data));
        return redirect()->route('tilik_ks_table', $data->id)->with('success', 'Tilik berhasil disimpan');
    }
}