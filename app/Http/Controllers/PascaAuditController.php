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

class PascaAuditController extends Controller
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
            $data = Dokumen::where('kategori', 'evaluasi')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'evaluasi')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($kategori == 'standar') {
            $keterangan = "Ketercapaian standar";
            $data = Dokumen::where('kategori', 'standar')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'standar')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } else {
            $keterangan = "Semua data";
            $data = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        }
        return view('pasca_audit.home_auditor', compact('data', 'years', 'keterangan'));
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
            $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $year])->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'evaluasi')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } elseif ($kategori == 'standar') {
            $keterangan = "Ketercapaian standar";
            $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $year])->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::where('kategori', 'standar')->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        } else {
            $keterangan = "Semua data";
            $data = Dokumen::where('tahun', $year)->whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->with('prodi')->latest('tahun')->paginate(15);
            $years = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->latest('tahun')->distinct()->pluck('tahun')->toArray();
        }
        return view('pasca_audit.home_auditor', compact('data', 'years', 'keterangan'));
    }


    public function index($kategori = null)
    {
        $user = Auth::user();
        if ($kategori == 'evaluasi') {
            $keterangan = 'Evaluasi diri';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'evaluasi'])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi'])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'evaluasi'])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi'])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            }
        } elseif ($kategori == 'standar') {
            $keterangan = 'Ketercapaian standar';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'standar'])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar'])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'standar'])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar'])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            }
        } else {
            $keterangan = 'Semua data';
            if ($user->role_id == 2) {
                $data = Dokumen::whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            }
        }
        return view('pasca_audit.home', compact('data', 'years', 'keterangan'));
    }

    public function filter_year($kategori = null, $year)
    {
        $user = Auth::user();
        if ($kategori == 'evaluasi') {
            $keterangan = 'Evaluasi diri';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi'])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi', 'prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'evaluasi'])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            }
        } elseif ($kategori == 'standar') {
            $keterangan = 'Ketercapaian standar';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar'])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar', 'prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['kategori' => 'standar'])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            }
        } else {
            $keterangan = 'Semua data';
            if ($user->role_id == 2) {
                $data = Dokumen::where(['tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->with('prodi')->paginate(15);
                $years = Dokumen::whereIn('status_id', [4, 5, 6, 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id, 'tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::where(['prodi_id' => $user->user_access_file[0]->prodi_id])->whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            } else {
                $data = Dokumen::where(['tahun' => $year])->whereIn('status_id', [4, 5, 6, 7])->with('prodi')->paginate(15);
                $years = Dokumen::whereIn('status_id', [4, 5, 6, 7])->distinct()->pluck('tahun')->toArray();
            }
        }
        return view('pasca_audit.home', compact('data', 'years', 'keterangan'));
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
        return view('pasca_audit.evaluasi', compact('sheetData', 'data'));
    }

    public function ed_table_save(Request $request)
    {
        $data = Dokumen::find($request->id);
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $worksheet = $file->getSheet(0);

        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);

        $boldCenter = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $komentarKey = 0;
        $nilaiKey = 0;

        foreach ($sheetData as $key => $sheet) {
            if ($sheet[0] == 'No') {
                $worksheet->setCellValue('K' . ($key + 1), 'Komentar');
                $worksheet->getStyle('K' . ($key + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('K' . ($key + 1))->applyFromArray($boldCenter);

                $worksheet->setCellValue('L' . ($key + 1), 'Nilai');
                $worksheet->getStyle('L' . ($key + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('L' . ($key + 1))->applyFromArray($boldCenter);
            } else {
                if ($sheet[3] && $sheet[1]) {
                    if ($request->input('komentar')) {
                        $komentar = $request->input('komentar');
                        $worksheet->setCellValue('K' . ($key + 1), $komentar[$komentarKey]);
                        $komentarKey++;
                    }

                    if ($request->input('nilai')) {
                        $nilai = $request->input('nilai');
                        $worksheet->setCellValue('L' . ($key + 1), $nilai[$nilaiKey] ?? 0);
                        $nilaiKey++;
                    }
                }
            }
        }

        $writer = IOFactory::createWriter($file, 'Xlsx');
        $this->DeleteFile($data->file_data);
        $writer->save(storage_path('app/public/' . $data->file_data));

        if ($request->input('komentar') && $request->input('nilai')) {
            $data->update(['status_id' => 6]);
            Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6]);
            activity()->log('Menambahkan komentar dan nilai pada ' . basename($data->file_data));
        } else {
            if ($request->input('nilai') && $data->status_id != 6 && $data->status_id != 4) {
                $data->update(['status_id' => 5]);
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 5]);
                activity()->log('Menambahkan nilai pada ' . basename($data->file_data));
            } elseif ($request->input('komentar') && $data->status_id != 6 && $data->status_id != 5) {
                $data->update(['status_id' => 4]);
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 4]);
                activity()->log('Menambahkan komentar pada ' . basename($data->file_data));
            } else {
                $data->update(['status_id' => 6]);
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6]);
                activity()->log('Mengubah data pada ' . basename($data->file_data));
            }
        }

        return redirect()->route('pasca_ed_table', $data->id)->with('success', 'Data berhasil disimpan');
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

        return view('pasca_audit.standar', compact('sheetData', 'sheetCount', 'headers', 'sheetName', 'data'));
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

        for ($i = 0; $i < $sheetCount - 2; $i++) {
            $worksheet = $file->getSheet($i);
            $komentarKey = 2;
            $nilaiKey = 2;

            if ($request->input('kategori') == 'komentar') {
                $worksheet->setCellValue('L1', 'Komentar');
                $worksheet->getStyle('L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('L1')->applyFromArray($boldCenter);

                $komentarData = $request->input($i . 'komentar');
                if (is_array($komentarData)) {
                    foreach ($komentarData as $value) {
                        $worksheet->setCellValue('L' . $komentarKey, $value ?? '');
                        $komentarKey++;
                    }
                }
            } elseif ($request->input('kategori') == 'nilai') {
                $worksheet->setCellValue('M1', 'Nilai');
                $worksheet->getStyle('M1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('M1')->applyFromArray($boldCenter);

                $nilaiData = $request->input($i . 'nilai');
                if (is_array($nilaiData)) {
                    foreach ($nilaiData as $value) {
                        $worksheet->setCellValue('M' . $nilaiKey, $value ?? '');
                        $nilaiKey++;
                    }
                }
            } else {
                $worksheet->setCellValue('L1', 'Komentar');
                $worksheet->getStyle('L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('L1')->applyFromArray($boldCenter);

                $komentarData = $request->input($i . 'komentar');
                if (is_array($komentarData)) {
                    foreach ($komentarData as $value) {
                        $worksheet->setCellValue('L' . $komentarKey, $value ?? '');
                        $komentarKey++;
                    }
                }

                $worksheet->setCellValue('M1', 'Nilai');
                $worksheet->getStyle('M1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('M1')->applyFromArray($boldCenter);

                $nilaiData = $request->input($i . 'nilai');
                if (is_array($nilaiData)) {
                    foreach ($nilaiData as $value) {
                        $worksheet->setCellValue('M' . $nilaiKey, $value ?? '');
                        $nilaiKey++;
                    }
                }
            }
        }

        $writer = IOFactory::createWriter($file, 'Xlsx');
        $this->DeleteFile($data->file_data);
        $writer->save(storage_path('app/public/' . $data->file_data));

        if ($request->input('kategori') == 'komentarnilai' || $request->input('kategori') == 'nilaikomentar') {
            $data->update(['status_id' => 6]);
            Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6]);
            activity()->log('Menambahkan komentar dan nilai pada ' . basename($data->file_data));
        } else {
            if ($request->input('kategori') == 'nilai' && $data->status_id != 6 && $data->status_id != 4) {
                $data->update(['status_id' => 5]);
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 5]);
                activity()->log('Menambahkan nilai pada ' . basename($data->file_data));
            } elseif ($request->input('kategori') == 'komentar' && $data->status_id != 6 && $data->status_id != 5) {
                $data->update(['status_id' => 4]);
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 4]);
                activity()->log('Menambahkan komentar pada ' . basename($data->file_data));
            } else {
                $data->update(['status_id' => 6]);
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6]);
                activity()->log('Mengubah data pada ' . basename($data->file_data));
            }
        }

        return redirect()->route('pasca_ks_table', $data->id)->with('success', 'Data berhasil disimpan');
    }


    public function confirm($id)
    {
        $data = Dokumen::find($id);
        $data->update(['status_id' => 7]);
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 7]);
        activity()
            ->performedOn($data)
            ->log('Konfirmasi ' . basename($data->file_data));
        return redirect()->route('pasca_ed_table', $id)->with('success', 'Data disetujui');
    }

    public function cancel_confirm($id)
    {
        $data = Dokumen::find($id);
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6]);
        $data->update(['status_id' => 6]);
        activity()
            ->performedOn($data)
            ->log('Membatalkan konfirmasi ' . basename($data->file_data));
        return redirect()->route('pasca_ed_table', $id);
    }
}