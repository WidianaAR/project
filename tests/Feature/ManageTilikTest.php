<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageTilikTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function auditor_login()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'auditor@gmail.com',
            'password' => 'SimjamuTest123',
        ]);
        $this->assertAuthenticated();
    }

    private function user_access()
    {
        $user = User::find(4)->user_access_file;
        $access_prodi = [];
        foreach ($user as $value) {
            array_push($access_prodi, $value->prodi_id);
        }
        return $access_prodi;
    }

    public function test_tilik_page_rendered()
    {
        $this->auditor_login();
        $this->get('tilik')->assertStatus(200);
    }

    public function test_page_displays_a_list_of_files()
    {
        $this->auditor_login();
        $response = $this->get(route('tilik_home'));
        $access_prodi = $this->user_access();
        $datas = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->with('prodi', 'status', 'tahap')->latest('updated_at')->paginate(15);
        $response->assertViewIs('tilik.home')->assertViewHas('data', $datas);
    }

    public function test_page_displays_a_list_of_ed_tilik()
    {
        $this->auditor_login();
        $response = $this->post(route('tilik_filter', ['kategori' => 'evaluasi']));
        $access_prodi = $this->user_access();
        $datas = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->where('kategori', 'evaluasi')->with('prodi', 'status', 'tahap')->latest('updated_at')->paginate(15);
        $response->assertViewIs('tilik.home')->assertViewHas('data', $datas);
    }

    public function test_ed_tilik_table_page_rendered()
    {
        $this->auditor_login();
        $this->get('tilik/evaluasi/table/2')->assertStatus(200);
    }

    public function test_ed_tilik_save()
    {
        $this->auditor_login();
        $this->get('tilik/evaluasi/table/2');
        $data = [];
        for ($i = 0; $i < 83; $i++) {
            array_push($data, 'tilik');
        }
        $request = [
            'id' => 2,
            'tilik' => $data
        ];
        $this->post('tilik/evaluasi', $request)->assertRedirect('tilik/evaluasi/table/2')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_page_displays_a_list_of_ks_tilik()
    {
        $this->auditor_login();
        $response = $this->post(route('tilik_filter', ['kategori' => 'standar']));
        $access_prodi = $this->user_access();
        $datas = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [1, 2, 3])->where('kategori', 'standar')->with('prodi', 'status', 'tahap')->latest('updated_at')->paginate(15);
        $response->assertViewIs('tilik.home')->assertViewHas('data', $datas);
    }

    public function test_ks_tilik_table_page_rendered()
    {
        $this->auditor_login();
        $this->get('tilik/standar/table/18')->assertStatus(200);
    }

    public function test_ks_tilik_save()
    {
        $this->auditor_login();
        $this->get('tilik/standar/table/18');
        $request = [
            'id' => 18,
            '0tilik' => ['tilik1A', 'tilik1B'],
            '1tilik' => ['tilik2A', 'tilik2B'],
            '2tilik' => ['tilik3A', 'tilik3B'],
            '3tilik' => ['tilik4A', 'tilik4B'],
        ];
        $this->post('tilik/standar', $request)->assertRedirect('tilik/standar/table/18')->assertStatus(302)->assertSessionHas('success');
    }
}