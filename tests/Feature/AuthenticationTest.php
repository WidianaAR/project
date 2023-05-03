<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_rendered()
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_login_as_pjm()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('pjm')->assertStatus(302);
        $response = $this->get('pjm');
        $response->assertStatus(200)->assertSee('PJM');
    }

    public function test_login_as_kajur()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'kajur@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('kajur')->assertStatus(302);
        $response = $this->get('kajur');
        $response->assertStatus(200)->assertSee('Kajur');
    }

    public function test_login_as_koorprodi()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'koorprodi@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('koorprodi')->assertStatus(302);
        $response = $this->get('koorprodi');
        $response->assertStatus(200)->assertSee('Koorprodi');
    }

    public function test_login_as_auditor()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect('auditor')->assertStatus(302);
        $response = $this->get('auditor');
        $response->assertStatus(200)->assertSee('Auditor');
    }

    public function test_login_failed()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('login', [
            'email' => 'wardas@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $response->assertSessionHasErrors('login_gagal');
    }

    public function test_user_can_logout()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
        $this->get('logout')->assertRedirect('login')->assertStatus(302);
    }
}