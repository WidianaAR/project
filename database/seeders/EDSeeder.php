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


        // Data tahun 2022
        $path = $this->UploadFile($file, "Evaluasi Diri_Informatika_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 11,
                'tahun' => 2022,
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

        $path = $this->UploadFile($file, "Evaluasi Diri_Fisika_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 1,
                'tahun' => 2022,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Matematika_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 2,
                'tahun' => 2022,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Mesin_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 3,
                'tahun' => 2022,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Elektro_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 4,
                'tahun' => 2022,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Kimia_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 5,
                'tahun' => 2022,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Sipil_2022.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 7,
                'tahun' => 2022,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );


        // Data tahun 2021
        $path = $this->UploadFile($file, "Evaluasi Diri_Informatika_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 11,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Sistem Informasi_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 10,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Fisika_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 1,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Matematika_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 2,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Mesin_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 3,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Elektro_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 4,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Kimia_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 5,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );

        $path = $this->UploadFile($file, "Evaluasi Diri_Teknik Sipil_2021.xlsx");
        EvaluasiDiri::create(
            [
                'prodi_id' => 7,
                'tahun' => 2021,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null
            ]
        );
    }
}