<?php

namespace Tests\Feature;

use Database\Seeders\AccountSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_rendered() {
        $this->get('/login')->assertStatus(200);
    }

    public function test_login_as_pjm() {
        $this->withoutExceptionHandling();
        $this->seed(RoleSeeder::class);
        $this->seed(AccountSeeder::class);
        $response = $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('pjm')->assertStatus(302);
    }

    public function test_login_as_kajur() {
        $this->withoutExceptionHandling();
        $this->seed(RoleSeeder::class);
        $this->seed(AccountSeeder::class);
        $response = $this->post('login', [
            'email' => 'kajur@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('kajur')->assertStatus(302);
    }

    public function test_login_as_koorprodi() {
        $this->withoutExceptionHandling();
        $this->seed(RoleSeeder::class);
        $this->seed(AccountSeeder::class);
        $response = $this->post('login', [
            'email' => 'koorprodi@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('koorprodi')->assertStatus(302);
    }

    public function test_login_as_auditor() {
        $this->withoutExceptionHandling();
        $this->seed(RoleSeeder::class);
        $this->seed(AccountSeeder::class);
        $response = $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('auditor')->assertStatus(302);
    }

    public function test_user_can_logout() {
        // $user = new User([
        //     'id_role' => 1,
        //     'name' => 'test PJM',
        //     'email' => 'testpjm@gmail.com',
        //     'password' => Hash::make('test'),
        // ]);
        // $this->actingAs($user);
        $this->withoutExceptionHandling();
        $this->seed(RoleSeeder::class);
        $this->seed(AccountSeeder::class);
        $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $this->get('logout')->assertRedirect('login')->assertStatus(302);
    }
}
