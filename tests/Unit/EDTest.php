<?php

namespace Tests\Unit;

use App\Models\Dokumen;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class EDTest extends TestCase
{
    use RefreshDatabase;

    // Model test
    public function test_evaluasi_diri_can_be_created()
    {
        $data = Dokumen::create([
            'file_data' => 'Files/Evaluasi Diri_Informatika_2023.xlsx',
            'prodi_id' => 11,
            'status_id' => 1,
            'kategori' => 'evaluasi',
            'tahun' => 2023,
        ]);

        $this->assertEquals('Files/Evaluasi Diri_Informatika_2023.xlsx', $data->file_data);
        $this->assertEquals(11, $data->prodi_id);
        $this->assertEquals(1, $data->status_id);
        $this->assertEquals('evaluasi', $data->kategori);
        $this->assertEquals(2023, $data->tahun);
    }

    // Controller test
    public function test_input_validation()
    {
        $path = public_path('files/ED_Template.xlsx');
        $file = new UploadedFile(
            $path,
            'test_file.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $data = ['file' => $file];
        $validator = Validator::make($data, [
            'file' => 'mimes:xlsx',
        ]);

        $this->assertTrue($validator->passes());
    }
}