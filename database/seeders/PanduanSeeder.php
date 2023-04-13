<?php

namespace Database\Seeders;

use App\Models\Panduan;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class PanduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ed_path = public_path("files/ED_Template.xlsx");
        $ed_file = new UploadedFile(
            $ed_path,
            'ED_Template.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $ks_path = public_path("files/KS_Template.xlsx");
        $ks_file = new UploadedFile(
            $ks_path,
            'ED_Template.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        Panduan::create([
            'judul' => 'Pengisian Evaluasi Diri Prorgram Studi',
            'keterangan' => '<div>Cara pengisian evaluasi diri :</div><ul><li>Unduh file template evaluasi diri yang tersedia di bawah</li><li>Buka file dan isi kolom nilai capaian dan link bukti</li><li>Simpan dan upload kembali file pada menu Evaluasi Diri</li></ul><div><br><em>Upload file hanya dapat dilakukan selama masa pengisian evaluasi diri berlangsung. Jika deadline pengisian evaluasi diri sudah habis maka file tidak dapat di-upload.</em></div>',
            'file_data' => $ed_file
        ]);

        Panduan::create([
            'judul' => 'Pengisian Ketercapaian Standar Prorgram Studi',
            'keterangan' => '<div>Cara pengisian ketercapaian standar :</div><ul><li>Unduh file template ketercapaian standar yang tersedia di bawah</li><li>Buka file dan isi kolom yang berwarna oranye atau kolom capaian serta kolom link bukti di setiap sheet</li><li>Simpan dan upload kembali file pada menu Ketercapaian Standar</li></ul><div><br><em>Upload file hanya dapat dilakukan selama masa pengisian ketercapaian standar berlangsung. Jika deadline pengisian ketercapaian standar sudah habis maka file tidak dapat di-upload.</em></div>',
            'file_data' => $ks_file
        ]);
    }
}