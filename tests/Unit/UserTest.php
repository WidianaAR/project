<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_user_can_be_created()
    {
        $user = User::create([
            'role_id' => 1,
            'name' => 'New User',
            'email' => 'new@gmail.com',
            'password' => '12345',
        ]);

        $this->assertEquals('1', $user->role_id);
        $this->assertEquals('New User', $user->name);
        $this->assertEquals('new@gmail.com', $user->email);
    }

    // Controller test
    public function test_page_displays_a_list_of_users()
    {
        $this->pjm_login();
        $response = $this->get('user');
        $users = User::with(['role', 'jurusan', 'prodi'])->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_pjms()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/pjm');
        $users = User::with('role')->where('role_id', 1)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_kajurs()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/kajur');
        $users = User::with(['role', 'jurusan'])->where('role_id', 2)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_koorprodis()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/koorprodi');
        $users = User::with(['role', 'jurusan', 'prodi'])->where('role_id', 3)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_auditors()
    {
        $this->pjm_login();
        $response = $this->get('user/filter/auditor');
        $users = User::with(['role', 'prodi'])->where('role_id', 4)->latest()->paginate(8);
        $response->assertViewIs('user.home')->assertViewHas('users', $users);
    }
}