<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageProdiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function login_pjm()
    {
        $this->withExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    public function test_prodi_page_rendered()
    {
        $this->login_pjm();
        $this->get('prodis')->assertStatus(200);
    }

    public function test_add_prodi_page_rendered()
    {
        $this->login_pjm();
        $this->get('prodis/create')->assertStatus(200);
    }

    public function test_add_new_prodi()
    {
        $this->login_pjm();
        $data = [
            'kode_prodi' => 30,
            'jurusan_id' => 1,
            'nama_prodi' => 'Prodi baru',
        ];
        $this->post('prodis', $data)->assertRedirect('prodis')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('prodis', $data);
    }

    public function test_edit_prodi_page_rendered()
    {
        $this->test_add_new_prodi();
        $this->get('prodis/24/edit')->assertStatus(200);
        // jika yang dijalankan hanya kelas ManageProdiTest maka id-nya dimulai dari 23
        // jika yang dijalankan semua test maka id-nya dimulai dari 24
    }

    public function test_edit_prodi()
    {
        $this->test_add_new_prodi();
        $data = [
            'kode_prodi' => 31,
            'jurusan_id' => 2,
            'nama_prodi' => 'Prodi edit',
        ];
        $this->put('prodis/25', $data)->assertRedirect('prodis')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('prodis', $data);
    }

    public function test_delete_prodi()
    {
        $this->test_add_new_prodi();
        $this->get('prodis')->assertStatus(200);
        $this->delete('prodis/26')->assertRedirect('prodis')->assertStatus(302)->assertSessionHas('success');
    }
}