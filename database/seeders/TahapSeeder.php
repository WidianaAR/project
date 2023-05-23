<?php

namespace Database\Seeders;

use App\Models\Tahap;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 32; $i++) {
            Tahap::create([
                'dokumen_id' => $i,
                'status_id' => 1,
                "created_at" => "2023-05-22 08:30:17",
                "updated_at" => "2023-05-22 08:30:17"
            ]);

            Tahap::create([
                'dokumen_id' => $i,
                'status_id' => 2,
                "created_at" => "2023-05-22 08:40:17",
                "updated_at" => "2023-05-22 08:40:17"
            ]);
        }
    }
}