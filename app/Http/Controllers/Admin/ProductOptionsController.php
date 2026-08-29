<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\ProductOptions;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ProductOptionsController extends Controller
{
    public function index()
    {
        try {
            $productOptions = ProductOptions::get();
            return view('admin.product-options.list', compact('productOptions'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            return view('admin.product-options.create');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $option = ProductOptions::create([
                'name' => $request->name,
            ]);

            return redirect()->route('admin-product-options-list')->with('success', 'Product option created successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function edit($token)
    {
        try {
            $productOptionId = decrypt($token);
            $productOption = ProductOptions::find($productOptionId);
            return view('admin.product-options.edit', compact('productOption'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $token)
    {
        try {
            $productOptionId = decrypt($token);
            $productOption = ProductOptions::find($productOptionId);

            $productOption = tap($productOption)->update([
                'name' => $request->name,
            ]);

            return redirect()->route('admin-product-options-list')->with('success', 'Product option updated successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function destory($token)
    {
        try {
            $productOptionId = decrypt($token);
            $productOption = ProductOptions::find($productOptionId);
            $productOption->delete();
            return redirect()->route('admin-product-options-list')->with('success', 'Product option deleted successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }
}
