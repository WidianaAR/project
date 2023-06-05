<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    public function UploadFile(UploadedFile $file, $filename = null)
    {
        $FileName = !is_null($filename) ? $filename : Str::random(10);
        return $file->storeAs(
            'Files',
            $FileName,
            'public'
        );
    }

    public function UploadFilePanduan(UploadedFile $file, $filename = null)
    {
        $FileName = !is_null($filename) ? $filename : Str::random(10);
        return $file->storeAs(
            'Panduans',
            $FileName,
            'public'
        );
    }

    public function DeleteFile($path)
    {
        Storage::disk('public')->delete($path);
    }

    public function ExportZip($zipname, $data)
    {
        $zip = new \ZipArchive();
        if ($zip->open(storage_path('app/public/' . $zipname), \ZipArchive::CREATE) == TRUE) {
            foreach ($data as $value) {
                $zip->addFile(storage_path('app/public/' . $value), $value);
            }
            $zip->close();
        }
    }

    public function DeleteZip($path)
    {
        Storage::disk('public')->delete($path);
    }

    public function ChangeFileName($old, $new)
    {
        Storage::move('public/' . $old, 'public/' . $new);
    }
}