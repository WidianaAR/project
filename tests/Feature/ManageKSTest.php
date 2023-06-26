<?php

namespace Tests\Feature;

use App\Models\Dokumen;
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
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
    }

    private function kajur_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'kajur@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
    }

    private function auditor_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => 'SimjamuTest123',
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

    public function test_page_displays_a_list_of_datas()
    {
        $this->pjm_login();
        $response = $this->get('standar');
        $data = Dokumen::where('kategori', 'standar')->with('prodi', 'prodi.jurusan', 'status', 'tahap')->latest('updated_at')->paginate(8);
        $response->assertViewIs('ketercapaian_standar.home')->assertViewHas('data', $data);
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
            'date' => '2023-10-01',
            'time' => '13:00',
        ];

        $this->post('standar/set_time', $data)->assertRedirect('standar')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_ks_time_end()
    {
        $this->test_ks_set_time();
        $this->get('standar/set_time/4')->assertStatus(302);
        $this->assertDatabaseHas('deadlines', [
            'id' => 4,
            'kategori' => 'standar',
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
        $this->assertDatabaseHas('dokumens', [
            'file_data' => 'Files/Instrumen Audit Mutu Internal_Informatika_2023.xlsx'
        ]);
    }

    public function test_ks_table_page_rendered()
    {
        $this->kajur_login();
        $this->get('standar/table/17')->assertStatus(200);
    }

    public function test_ks_edit_page_rendered()
    {
        $this->kajur_login();
        $this->get('standar/change/17')->assertStatus(200);
    }

    public function test_ks_edit_file()
    {
        $this->kajur_login();
        $file = $this->dummy_file();
        $data = [
            'id_standar' => 17,
            'file' => $file,
            'prodi' => 18,
            'tahun' => 2023,
        ];
        $this->post('standar/change', $data)->assertRedirect('standar')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('dokumens', [
            'file_data' => 'Files/Instrumen Audit Mutu Internal_Bisnis Digital_2023.xlsx'
        ]);
    }

    public function test_ks_delete()
    {
        $this->kajur_login();
        $this->get('standar/delete/17')->assertRedirect('standar')->assertStatus(302);
        $this->assertDatabaseMissing('dokumens', [
            'prodi_id' => 11,
            'kategori' => 'standar',
            'tahun' => 2022
        ]);
    }

    public function test_ks_confirm()
    {
        $this->auditor_login();
        $this->get('pasca/confirm/17')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('dokumens', [
            'id' => 17,
            'status_id' => 7
        ]);
    }

    public function test_ks_cancel_confirm()
    {
        $this->auditor_login();
        $this->get('pasca/confirm/17')->assertStatus(302)->assertSessionHas('success');
        $this->get('pasca/cancel_confirm/17')->assertStatus(302);
        $this->assertDatabaseHas('dokumens', [
            'id' => 17,
            'status_id' => 6
        ]);
    }

    public function test_ks_export_file()
    {
        $this->test_ks_import_file();
        $this->pjm_login();
        $data = ['filename' => 'Files/Instrumen Audit Mutu Internal_Informatika_2023.xlsx'];
        $this->post('standar/export/file', $data)->assertStatus(200);
    }

    public function test_ks_export_all_file()
    {
        $this->test_ks_import_file();
        $this->pjm_login();

        $files = ['Files/Instrumen Audit Mutu Internal_Informatika_2023.xlsx', 'Files/Instrumen Audit Mutu Internal_Sistem Informasi_2022.xlsx'];
        $request = ['data' => $files];
        $this->post('standar/export', $request)->assertStatus(200);
    }
}