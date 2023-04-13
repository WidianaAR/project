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
        $users = User::latest()->get();
        $this->pjm_login();
        $this->get('user')->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_pjms()
    {
        $users = User::where('role_id', 1)->latest()->get();
        $this->pjm_login();
        $this->get('user/filter/pjm')->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_kajurs()
    {
        $users = User::where('role_id', 2)->latest()->get();
        $this->pjm_login();
        $this->get('user/filter/kajur')->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_koorprodis()
    {
        $users = User::where('role_id', 3)->latest()->get();
        $this->pjm_login();
        $this->get('user/filter/koorprodi')->assertViewIs('user.home')->assertViewHas('users', $users);
    }

    public function test_page_displays_a_list_of_auditors()
    {
        $users = User::where('role_id', 4)->latest()->get();
        $this->pjm_login();
        $this->get('user/filter/auditor')->assertViewIs('user.home')->assertViewHas('users', $users);
    }
}