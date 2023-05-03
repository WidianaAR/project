<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ManageKSChartTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function login()
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


    private function import_file()
    {
        $this->login();
        $path = public_path('files/KS_Template.xlsx');
        $file = new UploadedFile(
            $path,
            'test_file.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $data = [
            'prodi' => 11,
            'tahun' => 2022,
            'jurusan' => 1,
            'file' => $file,
        ];
        $this->post('standar', $data);
    }


    public function test_ks_chart_page_rendered()
    {
        $this->import_file();
        $this->auditor_login();
        $this->get('standar/confirm/1');
        $this->get('ks_chart')->assertStatus(200);
    }


    public function test_ks_chart_year_page_rendered()
    {
        $this->import_file();
        $this->auditor_login();
        $this->get('standar/confirm/1');

        $data = [
            'prodi' => 'all',
            'jurusan' => 'all',
            'tahun' => 2022
        ];
        $this->post('ks_chart', $data)->assertStatus(200)->assertSeeText('Ketercapaian standar semua jurusan tahun 2022');
    }


    public function test_ks_chart_jurusan_page_rendered()
    {
        $this->import_file();
        $this->auditor_login();
        $this->get('standar/confirm/1');

        $data = [
            'prodi' => 'all',
            'jurusan' => 1,
            'tahun' => 2022
        ];
        $this->post('ks_chart', $data)->assertStatus(200)->assertSeeText('Ketercapaian standar JMTI tahun 2022');
    }


    public function test_ks_chart_prodi_page_rendered()
    {
        $this->import_file();
        $this->auditor_login();
        $this->get('standar/confirm/1');

        $data = [
            'prodi' => 11,
            'jurusan' => 1,
            'tahun' => 2022
        ];
        $this->post('ks_chart', $data)->assertStatus(200)->assertSeeText('Ketercapaian standar program studi Informatika tahun 2022');
    }
}