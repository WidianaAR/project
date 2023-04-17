<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageFeedbackTest extends TestCase
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
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    public function test_feedback_page_rendered()
    {
        $this->auditor_login();
        $this->get('feedbacks')->assertStatus(200);
    }

    public function test_ed_feedback_page_rendered()
    {
        $this->auditor_login();
        $this->get('feedbacks/evaluasi')->assertStatus(200);
    }

    public function test_ed_feedback_year_page_rendered()
    {
        $this->auditor_login();
        $this->get('feedbacks/evaluasi/2023')->assertStatus(200);
    }

    public function test_ed_feedback_table_page_rendered()
    {
        $this->auditor_login();
        $this->get('feedbacks/evaluasi/table/9')->assertStatus(200);
    }

    public function test_ed_feedback_save()
    {
        $this->auditor_login();
        $this->get('feedbacks/evaluasi/table/9');
        $data = [];
        for ($i = 0; $i < 83; $i++) {
            array_push($data, 'temuan');
        }
        $request = [
            'id' => 9,
            'temuan' => $data
        ];
        $this->post('feedbacks/evaluasi', $request)->assertRedirect('feedbacks/evaluasi/table/9')->assertStatus(302)->assertSessionHas('success');
    }

    public function test_ks_feedback_page_rendered()
    {
        $this->auditor_login();
        $this->get('feedbacks/standar')->assertStatus(200);
    }

    public function test_ks_feedback_year_page_rendered()
    {
        $this->auditor_login();
        $this->get('feedbacks/standar/2023')->assertStatus(200);
    }

    public function test_ks_feedback_table_page_rendered()
    {
        $this->auditor_login();
        $this->get('feedbacks/standar/table/9')->assertStatus(200);
    }

    public function test_ks_feedback_save()
    {
        $this->auditor_login();
        $this->get('feedbacks/standar/table/9');
        $request = [
            'id' => 9,
            '0temuan' => ['temuan1A', 'temuan1B'],
            '1temuan' => ['temuan2A', 'temuan2B'],
            '2temuan' => ['temuan3A', 'temuan3B'],
            '3temuan' => ['temuan4A', 'temuan4B'],
        ];
        $this->post('feedbacks/standar', $request)->assertRedirect('feedbacks/standar/table/9')->assertStatus(302)->assertSessionHas('success');
    }
}