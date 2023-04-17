<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageJurusanTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function login_pjm()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    public function test_jurusan_page_rendered()
    {
        $this->login_pjm();
        $this->get('jurusans')->assertStatus(200);
    }

    public function test_add_jurusan_page_rendered()
    {
        $this->login_pjm();
        $this->get('jurusans/create')->assertStatus(200);
    }

    public function test_add_new_jurusan()
    {
        $this->login_pjm();
        $data = [
            'kode_jurusan' => 10,
            'nama_jurusan' => 'JB',
            'keterangan' => 'Jurusan Baru'
        ];
        $this->post('jurusans', $data)->assertRedirect('jurusans')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('jurusans', $data);
    }

    public function test_edit_jurusan_page_rendered()
    {
        $this->login_pjm();
        $this->get('jurusans/6/edit')->assertStatus(200);
    }

    public function test_edit_jurusan()
    {
        $this->login_pjm();
        $data = [
            'kode_jurusan' => 11,
            'nama_jurusan' => 'JE',
            'keterangan' => 'Jurusan Edit'
        ];
        $this->put('jurusans/6', $data)->assertRedirect('jurusans')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('jurusans', $data);
    }

    public function test_delete_jurusan()
    {
        $this->login_pjm();
        $this->get('jurusans')->assertStatus(200);
        $this->delete('jurusans/6')->assertRedirect('jurusans')->assertStatus(302)->assertSessionHas('success');
    }
}