<?php

namespace Database\Seeders;

use App\Models\Panduan;
use App\Traits\FileTrait;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class PanduanSeeder extends Seeder
{
    use FileTrait;
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
            'Template instrumen simulasi akreditasi.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
        $ed_file = $this->UploadFilePanduan($ed_file, 'Template instrumen simulasi akreditasi.xlsx');

        $ks_path = public_path("files/KS_Template.xlsx");
        $ks_file = new UploadedFile(
            $ks_path,
            'Template instrumen audit mutu internal.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
        $ks_file = $this->UploadFilePanduan($ks_file, 'Template instrumen audit mutu internal.xlsx');

        $pjm_path = public_path("files/User Manual PJM.pdf");
        $pjm_file = new UploadedFile(
            $pjm_path,
            'User Manual PJM.pdf',
            'pdf',
            null,
            true
        );
        $pjm_file = $this->UploadFilePanduan($pjm_file, 'User Manual Kajur.pdf');

        $kajur_path = public_path("files/User Manual Kajur.pdf");
        $kajur_file = new UploadedFile(
            $kajur_path,
            'User Manual Kajur.pdf',
            'pdf',
            null,
            true
        );
        $kajur_file = $this->UploadFilePanduan($kajur_file, 'User Manual Kajur.pdf');

        $koorprodi_path = public_path("files/User Manual Koorprodi.pdf");
        $koorprodi_file = new UploadedFile(
            $koorprodi_path,
            'User Manual Koorprodi.pdf',
            'pdf',
            null,
            true
        );
        $koorprodi_file = $this->UploadFilePanduan($koorprodi_file, 'User Manual Koorprodi.pdf');

        $auditor_path = public_path("files/User Manual Auditor.pdf");
        $auditor_file = new UploadedFile(
            $auditor_path,
            'User Manual Auditor.pdf',
            'pdf',
            null,
            true
        );
        $auditor_file = $this->UploadFilePanduan($auditor_file, 'User Manual Auditor.pdf');



        Panduan::create([
            'judul' => 'Pengisian Instrumen Simulasi Akreditasi Program Studi',
            'keterangan' => '<div>Cara pengisian instrumen simulasi akreditasi :</div><ul><li>Unduh file template instrumen simulasi akreditasi yang tersedia di bawah</li><li>Buka file dan isi kolom nilai capaian dan link bukti</li><li>Simpan dan upload kembali file instrumen pada menu Simulasi Akreditasi</li></ul><div><br><em>Upload file hanya dapat dilakukan selama masa pengisian instrumen simulasi akreditasi berlangsung. Jika deadline pengisian sudah berakhir maka file tidak dapat diunggah.</em></div>',
            'file_data' => $ed_file
        ]);

        Panduan::create([
            'judul' => 'Pengisian Instrumen Audit Mutu Internal Program Studi',
            'keterangan' => '<div>Cara pengisian instrumen audit mutu internal :</div><ul><li>Unduh file template instrumen audit mutu internal yang tersedia di bawah</li><li>Buka file dan isi kolom berwarna oranye atau kolom capaian serta kolom link bukti di setiap sheet</li><li>Simpan dan upload kembali file instrumen pada menu Audit Mutu Internal</li></ul><div><br><em>Upload file hanya dapat dilakukan selama masa pengisian instrumen audit mutu internal berlangsung. Jika deadline pengisian sudah berakhir maka file tidak dapat diunggah.</em></div>',
            'file_data' => $ks_file
        ]);

        Panduan::create([
            'judul' => 'Panduan Penggunaan Aplikasi untuk PJM',
            'keterangan' => '<div>User manual atau petunjuk penggunaan aplikasi untuk pengguna dengan role PJM dapat diunduh pada tautan di bawah ini.</div>',
            'file_data' => $pjm_file
        ]);

        Panduan::create([
            'judul' => 'Panduan Penggunaan Aplikasi untuk Ketua Jurusan',
            'keterangan' => '<div>User manual atau petunjuk penggunaan aplikasi untuk pengguna dengan role Ketua Jurusan dapat diunduh pada tautan di bawah ini.</div>',
            'file_data' => $kajur_file
        ]);

        Panduan::create([
            'judul' => 'Panduan Penggunaan Aplikasi untuk Koordinator Program Studi',
            'keterangan' => '<div>User manual atau petunjuk penggunaan aplikasi untuk pengguna dengan role Koordinator Program Studi dapat diunduh pada tautan di bawah ini.</div>',
            'file_data' => $koorprodi_file
        ]);

        Panduan::create([
            'judul' => 'Panduan Penggunaan Aplikasi untuk Auditor',
            'keterangan' => '<div>User manual atau petunjuk penggunaan aplikasi untuk pengguna dengan role Auditor dapat diunduh pada tautan di bawah ini.</div>',
            'file_data' => $auditor_file
        ]);
    }
}