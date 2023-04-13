<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiDiri;
use App\Models\KetercapaianStandar;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FeedbackController extends Controller
{
    use FileTrait;

    public function index($kategori = null)
    {
        $user = Auth::user();
        if ($kategori == 'evaluasi' || $kategori == null) {
            $data_standar = null;
            $keterangan = 'Semua data evaluasi diri';
            if ($user->role_id == 2) {
                $data_evaluasi = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->jurusan_id);
                })->with('prodi')->get();
                $years = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3 || $user->role_id == 4) {
                $data_evaluasi = EvaluasiDiri::where('prodi_id', $user->prodi_id)->with(['prodi', 'prodi.jurusan'])->get();
                $years = EvaluasiDiri::where('prodi_id', $user->prodi_id)->distinct()->pluck('tahun')->toArray();
            } else {
                $data_evaluasi = EvaluasiDiri::with('prodi')->get();
                $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();
            }
        } else {
            $data_evaluasi = null;
            $keterangan = 'Semua data ketercapaian standar';
            if ($user->role_id == 2) {
                $data_standar = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->jurusan_id);
                })->with('prodi')->get();
                $years = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->jurusan_id);
                })->distinct()->pluck('tahun')->toArray();
            } elseif ($user->role_id == 3 || $user->role_id == 4) {
                $data_standar = KetercapaianStandar::where('prodi_id', $user->prodi_id)->with(['prodi', 'prodi.jurusan'])->get();
                $years = KetercapaianStandar::where('prodi_id', $user->prodi_id)->distinct()->pluck('tahun')->toArray();
            } else {
                $data_standar = KetercapaianStandar::with('prodi')->get();
                $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
            }
        }
        return view('feedback.home', compact('data_evaluasi', 'data_standar', 'years', 'keterangan'));
    }

    public function filter_year($kategori = null, $year)
    {
        $user = Auth::user();
        if ($year == 'all') {
            return redirect()->route('feedback', $kategori);
        } else {
            if ($kategori == 'evaluasi' || $kategori == null) {
                $data_standar = null;
                $keterangan = 'Eevaluasi diri ' . $year;
                if ($user->role_id == 2) {
                    $data_evaluasi = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                        $query->where('id', $user->jurusan_id);
                    })->where('tahun', $year)->with('prodi')->get();
                    $years = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                        $query->where('id', $user->jurusan_id);
                    })->distinct()->pluck('tahun')->toArray();
                } elseif ($user->role_id == 3 || $user->role_id == 4) {
                    $data_evaluasi = EvaluasiDiri::where(['prodi_id' => $user->prodi_id, 'tahun' => $year])->with(['prodi', 'prodi.jurusan'])->get();
                    $years = EvaluasiDiri::where('prodi_id', $user->prodi_id)->distinct()->pluck('tahun')->toArray();
                } else {
                    $data_evaluasi = EvaluasiDiri::where('tahun', $year)->with('prodi')->get();
                    $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();
                }
            } else {
                $data_evaluasi = null;
                $keterangan = 'Ketercapaian standar ' . $year;
                if ($user->role_id == 2) {
                    $data_standar = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                        $query->where('id', $user->jurusan_id);
                    })->where('tahun', $year)->with('prodi')->get();
                    $years = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                        $query->where('id', $user->jurusan_id);
                    })->distinct()->pluck('tahun')->toArray();
                } elseif ($user->role_id == 3 || $user->role_id == 4) {
                    $data_standar = KetercapaianStandar::where(['prodi_id' => $user->prodi_id, 'tahun' => $year])->with(['prodi', 'prodi.jurusan'])->get();
                    $years = KetercapaianStandar::where('prodi_id', $user->prodi_id)->distinct()->pluck('tahun')->toArray();
                } else {
                    $data_standar = KetercapaianStandar::where('tahun', $year)->with('prodi')->get();
                    $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
                }
            }
            return view('feedback.home', compact('data_evaluasi', 'data_standar', 'years', 'keterangan'));
        }
    }

    public function ed_table($id)
    {
        $data = EvaluasiDiri::find($id)->load('prodi');
        $user = Auth::user();

        if ($user->role_id == 3 || $user->role_id == 4 && $data->prodi_id != $user->prodi_id) {
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif ($user->role_id == 2 && $data->prodi->jurusan->id != $user->jurusan_id) {
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);
        $temuan = (array_key_exists(9, $sheetData[0])) ? 'not null' : null;
        return view('feedback.ed_detail', compact('sheetData', 'data', 'temuan'));
    }

    public function ed_table_save(Request $request)
    {
        $temuan = $request->input('temuan');
        $data = EvaluasiDiri::find($request->id);
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $worksheet = $file->getSheet(0);

        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);
        $temuanKey = 0;

        $boldCenter = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        foreach ($sheetData as $key => $sheet) {
            if ($sheet[0] == 'No') {
                $worksheet->setCellValue('J' . ($key + 1), 'Temuan');
                $worksheet->getStyle('J' . ($key + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
                $worksheet->getStyle('J' . ($key + 1))->applyFromArray($boldCenter);
            } else {
                if ($sheet[3] && $sheet[1]) {
                    $worksheet->setCellValue('J' . ($key + 1), $temuan[$temuanKey]);
                    $temuanKey++;
                }
            }
        }

        $writer = IOFactory::createWriter($file, 'Xlsx');
        $this->DeleteFile($data->file_data);
        $writer->save(storage_path('app/public/' . $data->file_data));

        return redirect()->route('fb_ed_table', $data->id)->with('success', 'Temuan berhasil disimpan');
    }

    public function ks_table($id)
    {
        $headers = array();
        $sheetData = array();

        $user = Auth::user();
        $data = KetercapaianStandar::find($id)->load('prodi');

        if ($user->role_id == 3 || $user->role_id == 4 && $data->prodi_id != $user->prodi_id) {
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

        $temuan = (array_key_exists('K', $sheetData[0][0])) ? 'not null' : null;
        return view('feedback.ks_detail', compact('sheetData', 'headers', 'sheetName', 'data', 'temuan'));
    }

    public function ks_table_save(Request $request)
    {
        $temuan1 = $request->input('0temuan');
        $temuan2 = $request->input('1temuan');
        $temuan3 = $request->input('2temuan');
        $temuan4 = $request->input('3temuan');

        $data = KetercapaianStandar::find($request->id);
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));

        $boldCenter = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $worksheet = $file->getSheet(0);
        $rowIndex = 2;
        $worksheet->setCellValue('K1', 'Temuan');
        $worksheet->getStyle('K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
        $worksheet->getStyle('K1')->applyFromArray($boldCenter);
        foreach ($temuan1 as $value) {
            $worksheet->setCellValue('K' . $rowIndex, $value);
            $rowIndex++;
        }

        $worksheet = $file->getSheet(1);
        $rowIndex = 2;
        $worksheet->setCellValue('K1', 'Temuan');
        $worksheet->getStyle('K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
        $worksheet->getStyle('K1')->applyFromArray($boldCenter);
        foreach ($temuan2 as $value) {
            $worksheet->setCellValue('K' . $rowIndex, $value);
            $rowIndex++;
        }

        $worksheet = $file->getSheet(2);
        $rowIndex = 2;
        $worksheet->setCellValue('K1', 'Temuan');
        $worksheet->getStyle('K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
        $worksheet->getStyle('K1')->applyFromArray($boldCenter);
        foreach ($temuan3 as $value) {
            $worksheet->setCellValue('K' . $rowIndex, $value);
            $rowIndex++;
        }

        $worksheet = $file->getSheet(3);
        $rowIndex = 2;
        $worksheet->setCellValue('K1', 'Temuan');
        $worksheet->getStyle('K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CC9DFF');
        $worksheet->getStyle('K1')->applyFromArray($boldCenter);
        foreach ($temuan4 as $value) {
            $worksheet->setCellValue('K' . $rowIndex, $value);
            $rowIndex++;
        }

        $writer = IOFactory::createWriter($file, 'Xlsx');
        $this->DeleteFile($data->file_data);
        $writer->save(storage_path('app/public/' . $data->file_data));

        return redirect()->route('fb_ks_table', $data->id)->with('success', 'Temuan berhasil disimpan');
    }
}