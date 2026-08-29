<?php
namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileUploadHelper
{
    public static function uploadToFrontendOnly(UploadedFile $file, string $type, string $frontendBasePath): ?string
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . "-{$type}." . $extension;
        $folderName = strtoupper(date('M') . date('Y')) . "/";
        $frontendPath = rtrim($frontendBasePath, '/') . '/' . $folderName;

        if (!File::exists($frontendPath)) {
            File::makeDirectory($frontendPath, 0777, true);
        }

        if ($file->move($frontendPath, $fileName)) {
            return $folderName . $fileName;
        }

        return null;
    }
}
