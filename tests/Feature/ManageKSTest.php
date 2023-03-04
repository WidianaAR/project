<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageKSTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function kajur_login() {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'kajur@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    public function pjm_login() {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    public function test_ks_page_rendered() {
        $this->kajur_login();
        $this->get('standar')->assertStatus(200);
    }

    public function test_ks_set_time_page_rendered() {
        $this->pjm_login();
        $this->get('standar/set_waktu')->assertStatus(200);
    }

    public function test_ks_set_time() {
        $this->pjm_login();
        $data = [
            'id' => 1,
            'date' => '2023-10-01',
            'time' => '13:00',
        ];

        $this->post('standar/set_waktu', $data)->assertRedirect('standar')->assertStatus(302);
    }

    public function test_ks_time_end() {
        $this->test_ks_set_time();
        $this->get('standar/set_waktu/1')->assertStatus(302);
    }
}
