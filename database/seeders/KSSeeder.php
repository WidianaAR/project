<?php

namespace Database\Seeders;

use App\Models\KetercapaianStandar;
use App\Traits\FileTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class KSSeeder extends Seeder
{
    use FileTrait;

    public function run()
    {
        $file = public_path("files/KS_Template.xlsx");
        $file = new UploadedFile(
            $file,
            'test_file.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $path = $this->UploadFile($file, "Ketercapaian Standar_Informatika_2023.xlsx");
        KetercapaianStandar::create(
            [
                'prodi_id' => 11,
                'tahun' => 2023,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Ketercapaian Standar_Sistem Informasi_2022.xlsx");
        KetercapaianStandar::create(
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