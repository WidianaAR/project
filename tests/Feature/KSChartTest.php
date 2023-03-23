<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KSChartTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }


    public function auditor_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }


    public function import_file()
    {
        $this->login();
        $path = public_path('template/KS_Template.xlsx');
        $file = new UploadedFile(
            $path,
            'test_file.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $data = [
            'prodi' => 11,
            'tahun' => 2023,
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
        $this->get('standar/confirm/2');

        $data = [
            'prodi' => 'all',
            'jurusan' => 'all',
            'tahun' => 2023
        ];
        $this->post('ks_chart', $data)->assertStatus(200)->assertSeeText('Ketercapaian standar semua jurusan tahun 2023');
    }


    public function test_ks_chart_jurusan_page_rendered()
    {
        $this->import_file();
        $this->auditor_login();
        $this->get('standar/confirm/3');

        $data = [
            'prodi' => 'all',
            'jurusan' => 1,
            'tahun' => 2023
        ];
        $this->post('ks_chart', $data)->assertStatus(200)->assertSeeText('Ketercapaian standar JMTI tahun 2023');
    }


    public function test_ks_chart_prodi_page_rendered()
    {
        $this->import_file();
        $this->auditor_login();
        $this->get('standar/confirm/4');

        $data = [
            'prodi' => 11,
            'jurusan' => 1,
            'tahun' => 2023
        ];
        $this->post('ks_chart', $data)->assertStatus(200)->assertSeeText('Ketercapaian standar program studi Informatika tahun 2023');
    }
}