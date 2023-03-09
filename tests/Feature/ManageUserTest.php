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

    public function pjm_login() {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    public function test_user_page_rendered() {
        $this->pjm_login();
        $this->get('user')->assertStatus(200);
    }
    
    public function test_add_user_page_rendered() {
        $this->pjm_login();
        $this->get('add_user')->assertStatus(200);
    }
    
    public function test_add_new_user() {
        $this->pjm_login();
        $user = [
            'role_id' => $this->faker->numberBetween(1, 4),
            'name' => $this->faker->name,
            'email' => 'test@gmail.com',
            'password' => '12345',
            'password_confirm' => '12345',
        ];
        $this->post('add_user', $user)->assertRedirect('user')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_edit_user_page_rendered() {
        $this->pjm_login();
        $this->get('change_user/1')->assertStatus(200);
    }

    public function test_edit_user() {
        $this->pjm_login();
        $data = [
            'id' => 1,
            'role_id' => $this->faker->numberBetween(1, 4),
            'name' => $this->faker->name,
            'email' => 'test@gmail.com',
        ];
        $this->post('change_user', $data)->assertRedirect('user')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_delete_user() {
        $this->pjm_login();
        $this->get('user/1')->assertRedirect('user')->assertStatus(302);
    }

    public function test_filter_user_pjm() {
        $this->pjm_login();
        $this->get('user/filter/pjm')->assertStatus(200);
    }

    public function test_filter_user_kajur() {
        $this->pjm_login();
        $this->get('user/filter/kajur')->assertStatus(200);
    }

    public function test_filter_user_koorprodi() {
        $this->pjm_login();
        $this->get('user/filter/koorprodi')->assertStatus(200);
    }

    public function test_filter_user_auditor() {
        $this->pjm_login();
        $this->get('user/filter/auditor')->assertStatus(200);
    }
}
