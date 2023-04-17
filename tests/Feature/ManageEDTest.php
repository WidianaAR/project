<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ManageEDTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function pjm_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    private function kajur_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'kajur@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    private function auditor_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    private function dummy_file()
    {
        $path = public_path('files/ED_Template.xlsx');
        $file = new UploadedFile(
            $path,
            'test_file.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
        return $file;
    }

    public function test_ed_page_rendered()
    {
        $this->kajur_login();
        $this->get('evaluasi')->assertStatus(200);
    }

    public function test_ed_set_time_page_rendered()
    {
        $this->pjm_login();
        $this->get('evaluasi/set_time')->assertStatus(200);
    }

    public function test_ed_set_time()
    {
        $this->pjm_login();
        $data = [
            'id' => 1,
            'date' => '2023-10-01',
            'time' => '13:00',
        ];

        $this->post('evaluasi/set_time', $data)->assertRedirect('evaluasi')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_ed_time_end()
    {
        $this->test_ed_set_time();
        $this->get('evaluasi/set_time/1')->assertStatus(302);
        $this->assertDatabaseHas('e_d_deadlines', [
            'id' => 1,
            'status' => 'finish',
        ]);
    }

    public function test_ed_add_page_rendered()
    {
        $this->kajur_login();
        $this->get('evaluasi/add')->assertStatus(200);
    }

    public function test_ed_import_file()
    {
        $this->kajur_login();
        $file = $this->dummy_file();
        $data = [
            'file' => $file,
            'prodi' => 11,
            'jurusan' => 1,
            'tahun' => 2023,
        ];
        $this->post('evaluasi', $data)->assertRedirect('evaluasi')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('evaluasi_diris', [
            'file_data' => 'Files/Evaluasi Diri_Informatika_2023.xlsx'
        ]);
    }

    public function test_ed_table_page_rendered()
    {
        $this->kajur_login();
        $this->get('evaluasi/table/1')->assertStatus(200);
    }

    public function test_ed_edit_page_rendered()
    {
        $this->kajur_login();
        $this->get('evaluasi/change/1')->assertStatus(200);
    }

    public function test_ed_edit_file()
    {
        $this->kajur_login();
        $file = $this->dummy_file();
        $data = [
            'id_evaluasi' => 1,
            'file' => $file,
            'prodi' => 18,
            'tahun' => 2023,
        ];
        $this->post('evaluasi/change', $data)->assertRedirect('evaluasi')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('evaluasi_diris', [
            'file_data' => 'Files/Evaluasi Diri_Bisnis Digital_2023.xlsx'
        ]);
    }

    public function test_ed_delete()
    {
        $this->kajur_login();
        $this->get('evaluasi/delete/1')->assertRedirect('evaluasi')->assertStatus(302);
        $data = [
            'prodi_id' => 11,
            'tahun' => 2022,
        ];
        $this->assertDatabaseMissing('evaluasi_diris', $data);
    }

    public function test_ed_confirm()
    {
        $this->auditor_login();
        $this->get('evaluasi/confirm/1')->assertRedirect('evaluasi/table/1')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('evaluasi_diris', [
            'id' => 1,
            'status' => 'disetujui'
        ]);
    }

    public function test_ed_cancel_confirm()
    {
        $this->auditor_login();
        $this->get('evaluasi/confirm/1')->assertRedirect('evaluasi/table/1')->assertStatus(302)->assertSessionHas('success');
        $this->get('evaluasi/cancel_confirm/1')->assertRedirect('evaluasi/table/1')->assertStatus(302);
        $this->assertDatabaseHas('evaluasi_diris', [
            'id' => 1,
            'status' => 'ditinjau'
        ]);
    }

    public function test_ed_feedback()
    {
        $this->auditor_login();
        $data = [
            'id_evaluasi' => 1,
            'feedback' => 'tes feedback'
        ];
        $this->post('evaluasi/feedback', $data)->assertRedirect('evaluasi/table/1')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('evaluasi_diris', [
            'id' => 1,
            'keterangan' => 'tes feedback'
        ]);
    }

    public function test_ed_export_file()
    {
        $this->test_ed_import_file();
        $this->pjm_login();
        $data = ['filename' => 'Files/Evaluasi Diri_Informatika_2023.xlsx'];
        $this->post('evaluasi/export/file', $data)->assertStatus(200);
    }

    public function test_ed_export_all_file()
    {
        $this->test_ed_import_file();
        $this->pjm_login();

        $files = ['Files/Evaluasi Diri_Informatika_2023.xlsx', 'Files/Evaluasi Diri_Sistem Informasi_2022.xlsx'];
        $request = ['data' => $files];
        $this->post('evaluasi/export', $request)->assertStatus(200);
    }

    public function test_ed_filter_year()
    {
        $this->test_ed_import_file();
        $this->get('evaluasi/filter/year/2023')->assertStatus(200);
    }

    public function test_ed_filter_jurusan()
    {
        $this->pjm_login();
        $this->get('evaluasi/filter/jurusan/1')->assertStatus(200);
    }

    public function test_ed_filter_prodi()
    {
        $this->pjm_login();
        $this->get('evaluasi/filter/prodi/11')->assertStatus(200);
    }
}