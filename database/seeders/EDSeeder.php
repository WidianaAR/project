<?php

namespace Database\Seeders;

use App\Models\EvaluasiDiri;
use App\Traits\FileTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class EDSeeder extends Seeder
{
    use FileTrait;

    public function run()
    {
        $file = public_path("files/ED_Template.xlsx");
        $file = new UploadedFile(
            $file,
            'test_file.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Informatika_2023.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 11,
                'tahun' => 2023,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Sistem Informasi_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 10,
                'tahun' => 2022,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );
    }
}