<?php

namespace Database\Seeders;

use App\Models\Dokumen;
use App\Traits\FileTrait;
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


        // Data tahun 2022
        $path = $this->UploadFile($file, "Evaluasi Diri_Informatika_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 11,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Sistem Informasi_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 10,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Fisika_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 1,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Matematika_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 2,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Mesin_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 3,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Elektro_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 4,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Kimia_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 5,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Sipil_2022.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 7,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2022,
                'file_data' => $path,
            ]
        );


        // Data tahun 2021
        $path = $this->UploadFile($file, "Evaluasi Diri_Informatika_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 11,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Sistem Informasi_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 10,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Fisika_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 1,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Matematika_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 2,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Mesin_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 3,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Elektro_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 4,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Kimia_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 5,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Sipil_2021.xlsx");
        Dokumen::create(
            [
                'prodi_id' => 7,
                'status_id' => 2,
                'kategori' => 'evaluasi',
                'tahun' => 2021,
                'file_data' => $path,
            ]
        );
    }
}