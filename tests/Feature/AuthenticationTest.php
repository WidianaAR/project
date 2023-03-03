<?php

namespace Tests\Feature;

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
        $response = $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('pjm')->assertStatus(302);
    }

    public function test_login_as_kajur() {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'kajur@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('kajur')->assertStatus(302);
    }

    public function test_login_as_koorprodi() {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'koorprodi@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('koorprodi')->assertStatus(302);
    }

    public function test_login_as_auditor() {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('auditor')->assertStatus(302);
    }

    public function test_user_can_logout() {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
        $this->get('logout')->assertRedirect('login')->assertStatus(302);
    }
}
