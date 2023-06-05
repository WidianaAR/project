<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
            ['keterangan' => 'perubahan diizinkan'],
            ['keterangan' => 'ditinjau'],
            ['keterangan' => 'berisi tilik'],
            ['keterangan' => 'berisi tilik dan komentar'],
            ['keterangan' => 'berisi tilik dan nilai akhir'],
            ['keterangan' => 'berisi tilik, komentar, dan nilai akhir'],
            ['keterangan' => 'dikonfirmasi'],
        ];

        foreach ($status as $value) {
            Status::create($value);
        }
    }
}