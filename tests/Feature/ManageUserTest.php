<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageUserTest extends TestCase
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

    public function test_user_page_rendered()
    {
        $this->pjm_login();
        $this->get('user')->assertStatus(200);
    }

    public function test_page_displays_a_list_of_users()
    {
        $this->pjm_login();
        $response = $this->get('user');
        $users = User::with(['role', 'user_access_file', 'user_access_file.jurusan', 'user_access_file.prodi'])->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_add_user_page_rendered()
    {
        $this->pjm_login();
        $this->get('user/add')->assertStatus(200);
    }

    public function test_add_new_user()
    {
        $this->pjm_login();
        $user = [
            'role_id' => 1,
            'name' => 'New user',
            'email' => 'new@gmail.com',
            'password' => 'SimjamuTest123',
            'confirm' => 'SimjamuTest123',
        ];
        $this->post('user/add', $user)->assertRedirect('user')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'New user',
            'email' => 'new@gmail.com',
        ]);
    }

    public function test_edit_user_page_rendered()
    {
        $this->pjm_login();
        $this->get('user/change/1')->assertStatus(200);
    }

    public function test_edit_user()
    {
        $this->pjm_login();
        $data = [
            'role_id' => 1,
            'name' => 'Edit User',
            'email' => 'edit@gmail.com',
        ];
        $this->post('user/change/1', $data)->assertRedirect('user')->assertStatus(302)->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'Edit User',
            'email' => 'edit@gmail.com',
        ]);
    }

    public function test_delete_user()
    {
        $this->pjm_login();
        $this->get('user/delete/1')->assertRedirect('user')->assertStatus(302);
    }

    public function test_page_displays_a_list_of_pjms()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/1');
        $users = User::with('role')->where('role_id', 1)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_kajurs()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/2');
        $users = User::with(['role', 'user_access_file', 'user_access_file.jurusan'])->where('role_id', 2)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_koorprodis()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/3');
        $users = User::with(['role', 'user_access_file', 'user_access_file.jurusan', 'user_access_file.prodi'])->where('role_id', 3)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_auditors()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/4');
        $users = User::with(['role', 'user_access_file', 'user_access_file.prodi'])->where('role_id', 4)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }
}