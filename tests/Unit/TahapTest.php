<?php

namespace Tests\Unit;

use App\Models\Tahap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahapTest extends TestCase
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

    // Model test
    public function test_tahap_can_be_created()
    {
        $tahap = Tahap::create([
            'dokumen_id' => 1,
            'status_id' => 3
        ]);

        $this->assertEquals('1', $tahap->dokumen_id);
        $this->assertEquals('3', $tahap->status_id);
    }
}