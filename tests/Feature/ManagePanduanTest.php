<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\TestCase;

class ManagePanduanTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function login_pjm()
    {
        $this->withoutExceptionHandling();
        $this->post('login', [
            'email' => 'pjm@gmail.com',
            'password' => '12345',
        ]);
        $this->assertAuthenticated();
    }

    private function file()
    {
        $path = public_path('files/panduan_dummy.pdf');
        $file = new UploadedFile(
            $path,
            'test_file.pdf',
            'pdf',
            null,
            true
        );
        return $file;
    }

    public function test_panduan_page_rendered()
    {
        $this->login_pjm();
        $this->get('panduans')->assertStatus(200);
    }

    public function test_add_panduan_page_rendered()
    {
        $this->login_pjm();
        $this->get('panduans/create')->assertStatus(200);
    }

    public function test_add_new_panduan()
    {
        $this->login_pjm();

        $data = [
            'judul' => 'Ini judul baru 2',
            'keterangan' => 'Ini keterangan baru',
            'file_data' => $this->file()
        ];
        $this->post('panduans', $data)->assertRedirect('panduans')->assertStatus(302)->assertSessionHas('success');

        $data['file_data'] = 'Panduans/Ini judul baru 2.pdf';
        $this->assertDatabaseHas('panduans', $data);
    }

    public function test_edit_panduan_page_rendered()
    {
        $this->test_add_new_panduan();
        $this->get('panduans/2/edit')->assertStatus(200);
    }

    public function test_edit_panduan()
    {
        $this->test_add_new_panduan();

        $data = [
            'judul' => 'Judul Edit',
            'keterangan' => 'Ini keterangan edit',
            'file_data' => $this->file()
        ];
        $this->put('panduans/3', $data)->assertRedirect('panduans')->assertStatus(302)->assertSessionHas('success');

        $data['file_data'] = 'Panduans/Judul Edit.pdf';
        $this->assertDatabaseHas('panduans', $data);
    }

    public function test_delete_panduan()
    {
        $this->test_add_new_panduan();
        $this->get('panduans')->assertStatus(200);
        $this->delete('panduans/4')->assertRedirect('panduans')->assertStatus(302)->assertSessionHas('success');
    }
}