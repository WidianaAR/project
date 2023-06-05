<?php

namespace Tests\Unit;

use App\Models\Dokumen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class KSTest extends TestCase
{
    use RefreshDatabase;

    // Model test
    public function test_ketercapaian_standar_can_be_created()
    {
        $data = Dokumen::create([
            'file_data' => 'Files/Ketercapaian Standar_Informatika_2023.xlsx',
            'prodi_id' => 11,
            'status_id' => 1,
            'kategori' => 'standar',
            'tahun' => 2023,
        ]);

        $this->assertEquals('Files/Ketercapaian Standar_Informatika_2023.xlsx', $data->file_data);
        $this->assertEquals(11, $data->prodi_id);
        $this->assertEquals(1, $data->status_id);
        $this->assertEquals('standar', $data->kategori);
        $this->assertEquals(2023, $data->tahun);
    }

    // Controller test
    public function test_input_validation()
    {
        $path = public_path('files/KS_Template.xlsx');
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