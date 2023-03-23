<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageUserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    use WithFaker;

    public function pjm_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    public function test_user_page_rendered()
    {
        $this->pjm_login();
        $this->get('user')->assertStatus(200);
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
            'role_id' => $this->faker->numberBetween(1, 4),
            'name' => 'New user',
            'email' => 'new@gmail.com',
            'password' => '12345',
            'password_confirm' => '12345',
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
            'id' => 1,
            'role_id' => $this->faker->numberBetween(1, 4),
            'name' => 'Edit User',
            'email' => 'edit@gmail.com',
        ];
        $this->post('user/change', $data)->assertRedirect('user')->assertStatus(302)->assertSessionHas('success');
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

    public function test_filter_user_pjm()
    {
        $this->pjm_login();
        $this->get('user/filter/pjm')->assertStatus(200);
    }

    public function test_filter_user_kajur()
    {
        $this->pjm_login();
        $this->get('user/filter/kajur')->assertStatus(200);
    }

    public function test_filter_user_koorprodi()
    {
        $this->pjm_login();
        $this->get('user/filter/koorprodi')->assertStatus(200);
    }

    public function test_filter_user_auditor()
    {
        $this->pjm_login();
        $this->get('user/filter/auditor')->assertStatus(200);
    }
}