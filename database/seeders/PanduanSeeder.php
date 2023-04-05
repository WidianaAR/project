<?php

namespace Database\Seeders;

use App\Models\Panduan;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class PanduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = storage_path('app/public/Panduans/panduan.pdf');
        $file = new UploadedFile(
            $path,
            'panduan.pdf',
            'pdf',
            null,
            true
        );

        $panduans = [
            'judul' => fake()->unique()->word(),
            'keterangan' => fake()->unique()->paragraphs(10, true),
            'file_data' => $file
        ];

        for ($i = 0; $i < 4; $i++) {
            Panduan::create($panduans);
        }
    }
}