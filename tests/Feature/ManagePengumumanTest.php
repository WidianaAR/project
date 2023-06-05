<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class ManagePengumumanTest extends TestCase
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

    public function test_add_new_pengumuman()
    {
        $this->withoutExceptionHandling();
        $this->pjm_login();
        $this->get('ed_chart');
        $data = ['judul' => 'Pengumuman Test', 'isi' => 'ini pengumuman'];
        $this->post('pengumuman', $data)->assertRedirect('ed_chart')->assertStatus(302);
        $this->assertDatabaseHas('pengumumans', $data);
    }

    public function test_close_pengumuman()
    {
        $this->test_add_new_pengumuman();
        $data = ['pengumuman_id' => [4]];
        $this->post('pengumuman/close', $data)->assertRedirect('ed_chart')->assertStatus(302);
        $this->assertDatabaseHas('pengumuman_users', [
            'user_id' => 1,
            'pengumuman_id' => 4
        ]);
    }

    public function test_display_pengumuman()
    {
        $this->pjm_login();
        $response = $this->get('ed_chart');
        $pengumuman = Pengumuman::latest()->first();
        $response->assertViewIs('dashboard.ed_chart')->assertViewHas('pengumuman', $pengumuman);
    }
}