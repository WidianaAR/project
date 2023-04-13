<?php

namespace Tests\Unit;

use App\Models\EvaluasiDiri;
use App\Models\KetercapaianStandar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Controller test
    public function test_page_displays_a_list_of_ed_feedbacks()
    {
        $datas = EvaluasiDiri::all();
        $this->pjm_login();
        $this->get(route('feedback'))->assertViewIs('feedback.home')->assertViewHas('data_evaluasi', $datas);
    }

    public function test_page_displays_a_list_of_ks_feedbacks()
    {
        $datas = KetercapaianStandar::all();
        $this->pjm_login();
        $this->get(route('feedback', 'standar'))->assertViewIs('feedback.home')->assertViewHas('data_standar', $datas);
    }
}