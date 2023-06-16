<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagePascaTest extends TestCase
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

    private function tilik_ks()
    {
        $request = [
            'id' => 18,
            '0tilik' => ['tilik1A', 'tilik1B'],
            '1tilik' => ['tilik2A', 'tilik2B'],
            '2tilik' => ['tilik3A', 'tilik3B'],
            '3tilik' => ['tilik4A', 'tilik4B'],
        ];
        $this->post('tilik/standar', $request);
    }

    public function test_pasca_page_rendered()
    {
        $this->auditor_login();
        $this->get('pasca')->assertStatus(200);
    }

    public function test_page_displays_a_list_of_files()
    {
        $this->auditor_login();
        $response = $this->post(route('pasca_filter'));
        $access_prodi = $this->user_access();
        $datas = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->with('prodi')->latest('updated_at')->paginate(15);
        $response->assertViewIs('pasca_audit.home')->assertViewHas('data', $datas);
    }

    public function test_page_displays_a_list_of_ed_pasca()
    {
        $this->auditor_login();
        $response = $this->post(route('pasca_filter', ['kategori' => 'evaluasi']));
        $access_prodi = $this->user_access();
        $datas = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->where('kategori', 'evaluasi')->with('prodi')->latest('updated_at')->paginate(15);
        $response->assertViewIs('pasca_audit.home')->assertViewHas('data', $datas);
    }

    public function test_ed_pasca_table_page_rendered()
    {
        $this->auditor_login();
        $this->get('pasca/evaluasi/table/2')->assertStatus(200);
    }

    public function test_ed_pasca_save()
    {
        $this->auditor_login();
        $this->get('pasca/evaluasi/table/2');
        $komentar = [];
        $nilai = [];
        for ($i = 0; $i < 83; $i++) {
            array_push($komentar, 'komentar');
            array_push($nilai, '3');
        }
        $request = [
            'id' => 2,
            'komentar' => $komentar,
            'nilai' => $komentar
        ];
        $this->post('pasca/evaluasi', $request)->assertRedirect('pasca/evaluasi/table/2')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_page_displays_a_list_of_ks_pasca()
    {
        $this->auditor_login();
        $response = $this->post(route('pasca_filter', ['kategori', 'standar']));
        $access_prodi = $this->user_access();
        $datas = Dokumen::whereIn('prodi_id', $access_prodi)->whereIn('status_id', [3, 4, 5, 6, 7])->where('kategori', 'standar')->with('prodi')->latest('updated_at')->paginate(15);
        $response->assertViewIs('pasca_audit.home')->assertViewHas('data', $datas);
    }

    public function test_ks_pasca_table_page_rendered()
    {
        $this->auditor_login();
        $this->tilik_ks();
        $this->get('pasca/standar/table/18')->assertStatus(200);
    }

    public function test_ks_pasca_save()
    {
        $this->auditor_login();
        $this->get('pasca/standar/table/18');
        $request = [
            'id' => 18,
            '0komentar' => ['komentar1A', 'komentar1B'],
            '1komentar' => ['komentar2A', 'komentar2B'],
            '2komentar' => ['komentar3A', 'komentar3B'],
            '3komentar' => ['komentar4A', 'komentar4B'],
            '0nilai' => ['3', '3'],
            '1nilai' => ['3', '3'],
            '2nilai' => ['3', '3'],
            '3nilai' => ['3', '3'],
        ];
        $this->post('pasca/standar', $request)->assertRedirect('pasca/standar/table/18')->assertStatus(302)->assertSessionHas('success');
    }
}