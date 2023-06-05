<?php

namespace Tests\Feature;

use App\Models\Prodi;
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
            'password' => 'SimjamuTest123',
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

    public function test_page_displays_a_list_of_prodis()
    {
        $this->login_pjm();
        $response = $this->get(route('prodis.index'));
        $prodis = Prodi::with('jurusan')->paginate(8);
        $response->assertViewIs('prodi.home')->assertViewHas('prodis', $prodis);
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
        $this->login_pjm();
        $this->get('prodis/20/edit')->assertStatus(200);
    }

    public function test_edit_prodi()
    {
        $this->login_pjm();
        $data = [
            'kode_prodi' => 31,
            'jurusan_id' => 2,
            'nama_prodi' => 'Prodi edit',
        ];
        $this->put('prodis/20', $data)->assertRedirect('prodis')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('prodis', $data);
    }

    public function test_delete_prodi()
    {
        $this->test_add_new_prodi();
        $this->get('prodis')->assertStatus(200);
        $this->delete('prodis/20')->assertRedirect('prodis')->assertStatus(302)->assertSessionHas('success');
    }
}