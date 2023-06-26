<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Prodi;
use App\Models\Tahap;
use App\Traits\FileTrait;
use App\Traits\TableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PascaAuditController extends Controller
{
    use FileTrait;
    use TableTrait;

    public function index(Request $request)
    {
        $user = Auth::user()->user_access_file;
        $access_prodi = [];
        foreach ($user as $value) {
            array_push($access_prodi, $value->prodi_id);
        }

        $keterangan = 'Semua data';
        $query = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7]);
        $query_year = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7]);
        $prodis = Prodi::whereIn('id', $access_prodi)->get();

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
            $query_year->where('kategori', $request->kategori);
            $keterangan = ($request->kategori == 'evaluasi') ? 'Evaluasi diri' : 'Ketercapaian standar';
        }

        if ($request->prodi) {
            $query->where('prodi_id', $request->prodi);
            $keterangan = $keterangan . ' ' . $request->prodi;
        }

        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
            $keterangan = $keterangan . ' ' . $request->tahun;
        }

        $data = $query->with('prodi')->latest('updated_at')->paginate(15);
        $years = $query_year->latest('tahun')->distinct()->pluck('tahun')->toArray();

        return view('pasca_audit.home', compact('data', 'years', 'keterangan', 'prodis'));
    }

    public function ed_table($id)
    {
        $table = $this->EDTable($id);
        $data = $table[0];
        $sheetData = $table[1];
        $user = Auth::user();

        $auditor_prodi = [];
        foreach ($user->user_access_file as $value) {
            array_push($auditor_prodi, $value->prodi_id);
        }

        if (!in_array($data->prodi_id, $auditor_prodi)) {
            activity()
                ->event('Simulasi akreditasi')
                ->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

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
                        $worksheet->setCellValue('K' . ($key + 1), $komentar[$komentarKey] ?? ' ');
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
            $data->touch();
            Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6])->touch();
            activity()
                ->event('Simulasi akreditasi')
                ->log('Menambahkan komentar dan nilai pada ' . basename($data->file_data));
        } else {
            if ($request->input('nilai') && $data->status_id != 6 && $data->status_id != 4) {
                $data->update(['status_id' => 5]);
                $data->touch();
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 5])->touch();
                activity()
                    ->event('Simulasi akreditasi')
                    ->log('Menambahkan nilai pada ' . basename($data->file_data));
            } elseif ($request->input('komentar') && $data->status_id != 6 && $data->status_id != 5) {
                $data->update(['status_id' => 4]);
                $data->touch();
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 4])->touch();
                activity()
                    ->event('Simulasi akreditasi')
                    ->log('Menambahkan komentar pada ' . basename($data->file_data));
            } else {
                $data->update(['status_id' => 6]);
                $data->touch();
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6])->touch();
                activity()
                    ->event('Simulasi akreditasi')
                    ->log('Mengubah data pada ' . basename($data->file_data));
            }
        }

        return redirect()->route('pasca_ed_table', $data->id)->with('success', 'Data berhasil disimpan');
    }


    public function ks_table($id)
    {
        $table = $this->KSTable($id);
        $data = $table[0];
        $headers = $table[1];
        $sheetCount = $table[2];
        $sheetName = $table[3];
        $sheetData = $table[4];
        $user = Auth::user();

        $auditor_prodi = [];
        foreach ($user->user_access_file as $value) {
            array_push($auditor_prodi, $value->prodi_id);
        }
        if (!in_array($data->prodi_id, $auditor_prodi)) {
            activity()
                ->event('Audit mutu internal')
                ->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
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
            $data->touch();
            Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6])->touch();
            activity()
                ->event('Audit mutu internal')
                ->log('Menambahkan komentar dan nilai pada ' . basename($data->file_data));
        } else {
            if ($request->input('kategori') == 'nilai' && $data->status_id != 6 && $data->status_id != 4) {
                $data->update(['status_id' => 5]);
                $data->touch();
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 5])->touch();
                activity()
                    ->event('Audit mutu internal')
                    ->log('Menambahkan nilai pada ' . basename($data->file_data));
            } elseif ($request->input('kategori') == 'komentar' && $data->status_id != 6 && $data->status_id != 5) {
                $data->update(['status_id' => 4]);
                $data->touch();
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 4])->touch();
                activity()
                    ->event('Audit mutu internal')
                    ->log('Menambahkan komentar pada ' . basename($data->file_data));
            } else {
                $data->update(['status_id' => 6]);
                $data->touch();
                Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6])->touch();
                activity()
                    ->event('Audit mutu internal')
                    ->log('Mengubah data pada ' . basename($data->file_data));
            }
        }

        return redirect()->route('pasca_ks_table', $data->id)->with('success', 'Data berhasil disimpan');
    }


    public function confirm($id)
    {
        $data = Dokumen::find($id);
        $data->update(['status_id' => 7]);
        $data->touch();
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 7])->touch();
        if ($data->kategori == 'evaluasi') {
            activity()
                ->performedOn($data)
                ->event('Simulasi akreditasi')
                ->log('Konfirmasi ' . basename($data->file_data));
        } else {
            activity()
                ->performedOn($data)
                ->event('Audit mutu internal')
                ->log('Konfirmasi ' . basename($data->file_data));
        }
        return redirect()->route('pasca_ed_table', $id)->with('success', 'Data disetujui');
    }

    public function cancel_confirm($id)
    {
        $data = Dokumen::find($id);
        $data->update(['status_id' => 6]);
        $data->touch();
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 6])->touch();
        if ($data->kategori == 'evaluasi') {
            activity()
                ->performedOn($data)
                ->event('Simulasi akreditasi')
                ->log('Membatalkan konfirmasi ' . basename($data->file_data));
        } else {
            activity()
                ->performedOn($data)
                ->event('Audit mutu internal')
                ->log('Membatalkan konfirmasi ' . basename($data->file_data));
        }
        return redirect()->route('pasca_ed_table', $id);
    }
}