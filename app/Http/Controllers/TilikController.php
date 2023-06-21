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

class TilikController extends Controller
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
        $query = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3]);
        $query_year = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3]);
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

        return view('tilik.home', compact('data', 'years', 'keterangan', 'prodis'));
    }

    public function change_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'mimes:xlsx',
            ], [
                    'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
                ]);

            $spreadsheet = IOFactory::load($request->file('file'));
            $sheet = $spreadsheet->getSheet(0);
            $fileColumns = [];

            if ($request->kategori == 'evaluasi') {
                $columns = ['Standar', 'Kriteria', 'Nilai capaian', 'Sebutan', 'Bobot', 'Nilai Tertimbang', 'Link Bukti'];
                for ($column = 'C'; $column <= 'I'; $column++) {
                    $cellValue = $sheet->getCell($column . 2)->getValue();
                    array_push($fileColumns, $cellValue);
                }

                $missingColumns = array_diff($columns, $fileColumns);
                if (count($missingColumns) > 0) {
                    return back()->with('error', 'Mohon periksa kembali file yang Anda unggah! Kolom yang diperlukan tidak ditemukan: ' . join(', ', $missingColumns));
                }
            } else {
                $columns = ['Standar', 'NO', 'PERNYATAAN ISI STANDAR ', 'INDIKATOR ', '', '', 'Satuan'];
                for ($column = 'A'; $column <= 'G'; $column++) {
                    $cellValue = $sheet->getCell($column . 1)->getValue();
                    array_push($fileColumns, $cellValue);
                }
                $missingColumns = array_diff($columns, $fileColumns);
                if (count($missingColumns) > 0) {
                    return back()->with('error', 'Mohon periksa kembali file yang Anda unggah! Kolom yang diperlukan tidak ditemukan: ' . join(', ', $missingColumns));
                }
            }


            $kategori = ($request->kategori == 'evaluasi') ? 'Evaluasi Diri_' : 'Ketercapaian Standar_';
            $data = Dokumen::find($request->id);
            $this->DeleteFile($data->file_data);
            $extension = $request->file('file')->extension();
            $prodi = Prodi::find($request->prodi);
            $path = $this->UploadFile($request->file('file'), $kategori . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            $data->update(
                [
                    'prodi_id' => $request->prodi,
                    'status_id' => 2,
                    'file_data' => $path,
                ]
            );
            $data->touch();
            Tahap::where(['dokumen_id' => $data->id, 'status_id' => 2])->first()->touch();

            if ($request->kategori == 'evaluasi') {
                activity()
                    ->performedOn($data)
                    ->event('Evaluasi diri')
                    ->log('Mengubah data evaluasi diri dengan id ' . $data->id);
            } else {
                activity()
                    ->performedOn($data)
                    ->event('Ketercapaian standar')
                    ->log('Mengubah data ketercapaian standar dengan id ' . $data->id);
            }
            return redirect()->back()->with('success', 'File berhasil diubah');
        }
        return redirect()->back()->with('error', 'File gagal diubah');
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
                ->event('Evaluasi diri')
                ->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

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
                    $worksheet->setCellValue('J' . ($key + 1), $tilik[$tilikKey] ?? ' ');
                    $tilikKey++;
                }
            }
        }

        $writer = IOFactory::createWriter($file, 'Xlsx');
        $this->DeleteFile($data->file_data);
        $writer->save(storage_path('app/public/' . $data->file_data));

        $data->update(['status_id' => 3]);
        $data->touch();
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 3])->touch();
        activity()
            ->event('Evaluasi diri')
            ->log('Menambahkan tilik pada ' . basename($data->file_data));
        return redirect()->route('tilik_ed_table', $data->id)->with('success', 'Tilik berhasil disimpan');
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
                ->event('Ketercapaian standar')
                ->log('Prohibited access | Mencoba akses data prodi lain');
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
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
        $data->touch();
        Tahap::updateOrCreate(['dokumen_id' => $data->id, 'status_id' => 3])->touch();
        activity()
            ->event('Ketercapaian standar')
            ->log('Menambahkan tilik pada ' . basename($data->file_data));
        return redirect()->route('tilik_ks_table', $data->id)->with('success', 'Tilik berhasil disimpan');
    }
}