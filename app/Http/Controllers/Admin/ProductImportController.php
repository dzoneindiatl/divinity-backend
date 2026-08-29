<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Tag;
use App\Models\User;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use App\Service\FileUploadService;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Validator, Response, Redirect, Str, View, File;

class ProductImportController extends Controller
{
    public $fileUploadService;
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->middleware('permission:view_product|create_product|edit_product|delete_product', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_product', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_product', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_product', ['only' => ['destroy']]);

        $this->fileUploadService = $fileUploadService;
        $this->listRouteName = 'admin-product-list';
        View()->share('listRouteName', $this->listRouteName);

        #echo  $method = request()->route()->getActionMethod(); exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ProductsImport, $request->file('file'));

        return back()->with('success', 'Products imported successfully!');
    }

    
}
