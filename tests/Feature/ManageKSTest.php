<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ManageKSTest extends TestCase
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
        $path = public_path('files/KS_Template.xlsx');
        $file = new UploadedFile(
            $path,
            'test_file.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
        return $file;
    }

    public function test_ks_page_rendered()
    {
        $this->kajur_login();
        $this->get('standar')->assertStatus(200);
    }

    public function test_ks_set_time_page_rendered()
    {
        $this->pjm_login();
        $this->get('standar/set_time')->assertStatus(200);
    }

    public function test_ks_set_time()
    {
        $this->pjm_login();
        $data = [
            'id' => 1,
            'date' => '2023-10-01',
            'time' => '13:00',
        ];

        $this->post('standar/set_time', $data)->assertRedirect('standar')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_ks_time_end()
    {
        $this->test_ks_set_time();
        $this->get('standar/set_time/1')->assertStatus(302);
        $this->assertDatabaseHas('k_s_deadlines', [
            'id' => 1,
            'status' => 'finish',
        ]);
    }

    public function test_ks_add_page_rendered()
    {
        $this->kajur_login();
        $this->get('standar/add')->assertStatus(200);
    }

    public function test_ks_import_file()
    {
        $this->kajur_login();
        $file = $this->dummy_file();
        $data = [
            'file' => $file,
            'prodi' => 11,
            'jurusan' => 1,
            'tahun' => 2023,
        ];
        $this->post('standar', $data)->assertRedirect('standar')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('ketercapaian_standars', [
            'file_data' => 'Files/Ketercapaian Standar_Informatika_2023.xlsx'
        ]);
    }

    public function test_ks_table_page_rendered()
    {
        $this->test_ks_import_file();
        $this->get('standar/table/1')->assertStatus(200);
    }

    public function test_ks_edit_page_rendered()
    {
        $this->test_ks_import_file();
        $this->get('standar/change/1')->assertStatus(200);
    }

    public function test_ks_edit_file()
    {
        $this->test_ks_import_file();
        $file = $this->dummy_file();
        $data = [
            'id_standar' => 1,
            'file' => $file,
            'prodi' => 18,
            'tahun' => 2023,
        ];
        $this->post('standar/change', $data)->assertRedirect('standar')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('ketercapaian_standars', [
            'file_data' => 'Files/Ketercapaian Standar_Bisnis Digital_2023.xlsx'
        ]);
    }

    public function test_ks_delete()
    {
        $this->test_ks_import_file();
        $this->get('standar/delete/1')->assertRedirect('standar')->assertStatus(302);
        $this->assertDatabaseMissing('ketercapaian_standars', [
            'prodi_id' => 11,
            'tahun' => 2023
        ]);
    }

    public function test_ks_confirm()
    {
        $this->test_ks_import_file();
        $this->auditor_login();
        $this->get('standar/confirm/1')->assertRedirect('standar')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('ketercapaian_standars', [
            'id' => 1,
            'status' => 'disetujui'
        ]);
    }

    public function test_ks_cancel_confirm()
    {
        $this->test_ks_import_file();
        $this->auditor_login();
        $this->get('standar/confirm/1')->assertRedirect('standar')->assertStatus(302)->assertSessionHas('success');
        $this->get('standar/cancel_confirm/1')->assertRedirect('standar')->assertStatus(302);
        $this->assertDatabaseHas('ketercapaian_standars', [
            'id' => 1,
            'status' => 'ditinjau'
        ]);
    }

    public function test_ks_feedback()
    {
        $this->test_ks_import_file();
        $this->auditor_login();
        $data = [
            'id_standar' => 1,
            'feedback' => 'tes feedback'
        ];
        $this->post('standar/feedback', $data)->assertRedirect('standar/table/1')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('ketercapaian_standars', [
            'id' => 1,
            'keterangan' => 'tes feedback'
        ]);
    }

    public function test_ks_export_file()
    {
        $this->test_ks_import_file();
        $this->pjm_login();
        $data = ['filename' => 'Files/Ketercapaian Standar_Informatika_2023.xlsx'];
        $this->post('standar/export/file', $data)->assertStatus(200);
    }

    public function test_ks_export_all_file()
    {
        $this->test_ks_import_file();
        $this->pjm_login();

        $files = ['Files/Ketercapaian Standar_Informatika_2023.xlsx', 'Files/Ketercapaian Standar_Sistem Informasi_2022.xlsx'];
        $request = ['data' => $files];
        $this->post('standar/export', $request)->assertStatus(200);
    }

    public function test_ks_filter_year()
    {
        $this->test_ks_import_file();
        $this->get('standar/filter/year/2023')->assertStatus(200);
    }

    public function test_ks_filter_jurusan()
    {
        $this->test_ks_import_file();
        $this->pjm_login();
        $this->get('standar/filter/jurusan/1')->assertStatus(200);
    }

    public function test_ks_filter_prodi()
    {
        $this->test_ks_import_file();
        $this->pjm_login();
        $this->get('standar/filter/prodi/11')->assertStatus(200);
    }
}