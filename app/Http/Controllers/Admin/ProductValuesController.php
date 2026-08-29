<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\ProductValues;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductOptions;

class ProductValuesController extends Controller
{
    public function index()
    {
        try {
            $productValues = ProductValues::with(['productOption'])->get();
            return view('admin.product-values.list', compact('productValues'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            $productOptions = ProductOptions::get();
            return view('admin.product-values.create',compact('productOptions'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $productOptionId = $request->product_option_id ?? null;
        try {
            $optionValues = ProductValues::create([
                'product_option_id' => $productOptionId,
                'name' => $request->name,

            ]);

            return redirect()->route('admin-product-options-values-list')->with('success', 'Product option value created successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function edit($token)
    {
        try {
            $productOptionValueId = decrypt($token);
            $productOptionValue = ProductValues::find($productOptionValueId);
            $productOptions = ProductOptions::get();
            return view('admin.product-values.edit', compact('productOptionValue','productOptions'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $token)
    {
        try {
            // dd($request->all());
            $productOptionValueId = decrypt($token);
            $productOptionValue = ProductValues::find($productOptionValueId);

            $productOptionId = $request->product_option_id ?? null;

            $productOptionValue = tap($productOptionValue)->update([
                'product_option_id' => $productOptionId,
                'name' => $request->name,
            ]);

            return redirect()->route('admin-product-options-values-list')->with('success', 'Product option value updated successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function destory($token)
    {
        try {
            $productOptionValueId = decrypt($token);
            $productOptionValue = ProductValues::find($productOptionValueId);
            $productOptionValue->delete();
            return redirect()->route('admin-product-options-values-list')->with('success', 'Product option value deleted successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }
}
