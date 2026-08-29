<?php

namespace App\Service;

use Illuminate\Support\Str;
use Maestroerror\HeicToJpg;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class FileUploadService
{
    public function uploadFile($file, $path, $driver = null)
    {
        if (is_null($driver)) {
            $driver = config('filesystems.default');
        }

        if (!Storage::disk($driver)->exists($path)) {
            Storage::disk($driver)->makeDirectory($path, 0777, true);
        }

        $uploadedFile = $file->storeAs(
            $path,
            $file->hashName(),
            $driver
        );

        return $uploadedFile;
    }

    public function deleteFromStorage($path, $driver = null)
    {
        if (is_null($driver)) {
            $driver = config('filesystems.default');
        }

        if (!empty($path)) {
            if (Storage::disk($driver)->exists($path)) {
                Storage::disk($driver)->delete($path);
            }
        }

        return true;
    }

    
}
