<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class FeedbackSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $feedbacks = [
            'prodi_id' => random_int(1, 20),
            'tanggal_audit' => fake(true)->unique()->date(),
            'keterangan' => fake(true)->unique()->paragraphs(10, true)
        ];

        for ($i = 0; $i < 4; $i++) {
            Feedback::create($feedbacks);
        }
    }
}