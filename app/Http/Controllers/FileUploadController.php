<?php

namespace App\Http\Controllers;

use App\Service\FileUploadService;
use Illuminate\Http\Request;
use File;

class FileUploadController extends Controller
{
    public $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function store(Request $request)
    {
        try {
            $path = $request->path ?? '';
            if ($request->hasFile('file') && !empty($path)) {
                // $fileExt = $request->file('file')->getClientOriginalExtension();                                
                $uploadedFile = $this->fileUploadService->uploadFile($request->file('file'), $path);
                $uploadFileUrl = url(\Storage::url($uploadedFile));

                return response()->json(['uploadedFile' => $uploadedFile ?? '', 'uploadFileUrl' => $uploadFileUrl ?? '', 'success' => true, 'message' => 'File Uploaded successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'File could not be uploaded'], 400);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['success' => false, 'message' => 'something is wrong', 'error_msg' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $name = $request->name;

            if (!empty($name)) {
                $this->fileUploadService->deleteFromStorage($name);
            }

            return response()->json(['name' => $name, 'success' => true, 'message' => 'File Deleted successfully'], 200);
        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json(['success' => false, 'message' => 'something is wrong', 'error_msg' => $e->getMessage()], 500);
        }
    }
    
    public function ckeditorUploadImage(Request $request){
        $uploadFileUrl = '';
        $message = 'File could not be uploaded.';
        $folderName = "cms-banner/".strtoupper(date('M') . date('Y')) . "/";
        $path = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
        if ($request->hasFile('upload') && !empty($path)) {
            $extension = $request->file('upload')->getClientOriginalExtension();
            $originalName = $request->file('upload')->getClientOriginalName();
            $fileName = time() . '.'. $extension;

            $folderName = "cms-banner/".strtoupper(date('M') . date('Y')) . "/";
            $folderPath = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, $mode = 0777, true);
            }
            if ($request->file('upload')->move($folderPath, $fileName)) {
                $uploadFileUrl = Config('constant.BANNER_IMAGE_URL').$folderName . $fileName;
            }
            $message = 'Image Uploaded Successfully.';
        }
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($request->CKEditorFuncNum, '$uploadFileUrl', '$message');</script>";
    }
}
