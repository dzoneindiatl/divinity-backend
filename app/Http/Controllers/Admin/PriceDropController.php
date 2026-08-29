<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Category;
use App\Models\PriceDrop;
use App\Models\Product;

use App\Models\ProductVariantCombination;
use App\Models\PriceDropAssign;
use App\Models\PriceDropLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Redirect, Response, Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PriceDropController extends Controller
{
    public $model = 'price-drops';
    public $listRouteName;
    public $request;
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_price_drop|create_price_drop|edit_price_drop|delete_price_drop', ['only' => ['index','store']]);
        $this->middleware('permission:create_price_drop', ['only' => ['create','store']]);
        $this->middleware('permission:edit_price_drop', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_price_drop', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-price-drops.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB = PriceDrop::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'price_drops.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);
            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['offset'])) {
                unset($searchData['offset']);
            }
            if (isset($searchData['limit'])) {
                unset($searchData['limit']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('price_drops.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('price_drops.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('price_drops.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                // if ($fieldValue != "") {

                // }
            }
        }
        $DB->where("is_deleted", 0);
        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();
        if ($request->ajax()) {

            return  View("admin.$this->model.load_more_data", compact('results', 'totalResults'));
        } else {

            return  View("admin.$this->model.index", compact('results', 'totalResults'));
        }
    }
    public function create(Request $request)
    {
        $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();
        $subcategory = Category::where('is_active', 1)->where('is_deleted', 0)->get();
        $subproducts = Product::where('is_active', 1)->where('is_deleted', 0)->get();
        return view("admin.$this->model.add", compact('categories', 'subcategory', 'subproducts'));
    }

    // public function create(Request $request)
    // {
    //     $categories = Category::whereNull('parent_id')
    //                     ->where('is_active', 1)
    //                     ->where('is_deleted', 0)
    //                     ->with('children') // Eager load the children relationship
    //                     ->get();

    //     $categoryHierarchy = [];

    //     foreach ($categories as $category) {
    //         $categoryHierarchy[] = [
    //             'id' => $category->id,
    //             'name' => $category->name
    //         ];

    //         // Check if the children relationship is not null and not empty
    //         if ($category->children && !$category->children->isEmpty()) {
    //             foreach ($category->children as $subCategory) {
    //                 $subCategoryName = $category->name . '->' . $subCategory->name;

    //                 // Check if sub category name already exists
    //                 $exists = false;
    //                 foreach ($categoryHierarchy as $cat) {
    //                     if ($cat['name'] === $subCategoryName) {
    //                         $exists = true;
    //                         break;
    //                     }
    //                 }

    //                 // If sub category name doesn't exist, add it
    //                 if (!$exists) {
    //                     $categoryHierarchy[] = [
    //                         'id' => $subCategory->id,
    //                         'name' => $subCategoryName
    //                     ];
    //                 }
    //             }
    //         }
    //     }

    //     $products = DB::table('products')->where('is_active', 1)
    //                 ->where('is_deleted', 0)
    //                 ->select('id','name')->get()->toArray();
    //     return view("admin.$this->model.add", ['categories' => $categoryHierarchy, 'products' => $products]);
    // }

    // public function edit(Request $request, $enuserid = null)
    // {
    //     $user_id = '';
    //     if (!empty($enuserid)) {

    //         $user_id = base64_decode($enuserid);
    //         $userDetails = PriceDrop::where('price_drops.id', $user_id)->first();

    //         $categories = Category::whereNull('parent_id')
    //             ->where('is_active', 1)
    //             ->where('is_deleted', 0)
    //             ->with('children') // Eager load the children relationship
    //             ->get();

    //         $categoryHierarchy = [];

    //         foreach ($categories as $category) {
    //             $categoryHierarchy[] = [
    //                 'id' => $category->id,
    //                 'name' => $category->name
    //             ];

    //             // Check if the children relationship is not null and not empty
    //             if ($category->children && !$category->children->isEmpty()) {
    //                 foreach ($category->children as $subCategory) {
    //                     $subCategoryName = $category->name . '->' . $subCategory->name;

    //                     // Check if sub category name already exists
    //                     $exists = false;
    //                     foreach ($categoryHierarchy as $cat) {
    //                         if ($cat['name'] === $subCategoryName) {
    //                             $exists = true;
    //                             break;
    //                         }
    //                     }

    //                     // If sub category name doesn't exist, add it
    //                     if (!$exists) {
    //                         $categoryHierarchy[] = [
    //                             'id' => $subCategory->id,
    //                             'name' => $subCategoryName
    //                         ];
    //                     }
    //                 }
    //             }
    //         }

    //         $products = DB::table('products')->where('is_active', 1)
    //             ->where('is_deleted', 0)
    //             ->select('id', 'name')->get()->toArray();

    //         $price_drop_assigned = PriceDropAssign::where('price_drop_id', $user_id)->pluck('reference_id')->toArray();

    //         return View("admin.$this->model.edit", ['userDetails' => $userDetails, 'categories' => $categoryHierarchy, 'products' => $products, 'price_drop_assigned' => $price_drop_assigned]);
    //     }
    // }
    public function edit(Request $request, $enuserid = null)
    {
        $price_drop_id = '';
        if (!empty($enuserid)) {

            $price_drop_id = base64_decode($enuserid);
            $price_drop_details = PriceDrop::with('price_drop_assigns')->where('id', $price_drop_id)->first();
            $assign_type = $price_drop_details->assign_type;
            $selected_category_id="";
            $selected_subcategory_id="";
            $selected_childcategory_id="";
            $price_drop_assigned = [];
           
            if (!empty($assign_type) && $assign_type == 'product') {
                

                $price_drop_assigned = PriceDropAssign::where('price_drop_id', $price_drop_id)->pluck('reference_id')->toArray();
                $final_category_id = Product::where('id', $price_drop_assigned[0])->select('category_id')->first();
                // dd($final_category_id);
                $selected_childcategory_id = $final_category_id['category_id'];
                // print_r($selected_childcategory_id);
                // die;
                $check_parent_id = Category::where('id', $selected_childcategory_id)->first();
                $check_another_parent = Category::where('id', $check_parent_id->parent_id)->first();
                $selected_subcategory_id = $check_parent_id->parent_id;
                $selected_category_id = $check_another_parent->parent_id;
            }

            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();
            $sub_categories = Category::whereNotNull('parent_id')
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->get();

            $products = DB::table('products')->where('is_active', 1)
                ->where('is_deleted', 0)
                ->select('id', 'name')->get()->toArray();
            // dd($price_drop_details);
            return View("admin.$this->model.edit", compact('categories', 'sub_categories', 'price_drop_details', 'selected_childcategory_id', 'selected_subcategory_id', 'selected_category_id'), ['products' => $products, 'price_drop_assigned' => $price_drop_assigned]);
        }
    }

    public function save(Request $request)
    {
        // Validate the input data
        $request->validate([
            'gain_type' => 'required|in:gain,drop',
            'drop_type' => 'required|in:flat,percentage',
            'amount' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'child_category_id' => 'nullable|exists:categories,id',
           // 'product_id' => 'nullable|array', // Handling multiple products
            //'product_id.*' => 'exists:products,id', // Validating each product id
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        // Store the price change record
        $priceChange = new PriceDrop();
        $priceChange->gain_type = $request->gain_type;
        $priceChange->drop_type = $request->drop_type;
        $priceChange->amount = $request->amount;
        $priceChange->category_id = $request->category_id;
        $priceChange->sub_category_id = $request->sub_category_id;
        $priceChange->child_category_id = $request->child_category_id;
        $priceChange->start_date = $request->start_date;
        $priceChange->end_date = $request->end_date;
        
        // If multiple products are selected, store their IDs as comma-separated values
        if ($request->product_id) {
            $priceChange->product_id = implode(',', $request->product_id);
        } else {
            $priceChange->product_id = null;
        }
        
        $priceChange->save();
        
        // Build query for filtering products based on categories and product IDs
        $query = Product::query();
        
        // Filter by category, sub-category, or child-category
        if ($request->category_id) {
            $query->where('main_category_id', $request->category_id);
        }
        if ($request->sub_category_id) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->child_category_id) {
            $query->where('child_category_id', $request->child_category_id);
        }
        
        // If product IDs are provided, filter by those product IDs
        if ($request->product_id) {
            $query->whereIn('id', $request->product_id);
        }
        
        // Get all the products that match the filtering criteria
        $products = $query->get();
        
        // Loop through all the products to update their buying_price and variants
        foreach ($products as $product) {
            // Get the current buying price for the product
            $baseBuyingPrice = $product->buying_price;
            
            // Apply price change to the buying price based on gain or drop
            if ($request->gain_type == 'gain') {
                if ($request->drop_type == 'flat') {
                    // Flat gain on buying price
                    $product->buying_price += $request->amount;
                } elseif ($request->drop_type == 'percentage') {
                    // Percentage gain on buying price
                    $product->buying_price += $product->buying_price * ($request->amount / 100);
                }
            } elseif ($request->gain_type == 'drop') {
                if ($request->drop_type == 'flat') {
                    // Flat drop on buying price
                    $product->buying_price -= $request->amount;
                } elseif ($request->drop_type == 'percentage') {
                    // Percentage drop on buying price
                    $product->buying_price -= $product->buying_price * ($request->amount / 100);
                }
            }
            
            // Now calculate the selling price based on the updated buying price and discount type
            $finalBuyingPrice = floor($product->buying_price); // Final updated buying price (rounded to 2 decimal places)
            
            // Calculate the final selling price based on the discount type
            if ($product->discount_type == 'flat') {
                // Flat discount calculation
                $product->selling_price = floor($finalBuyingPrice - $product->discount);
            } elseif ($product->discount_type == 'percentage') {
                // Percentage discount calculation
                $product->selling_price = floor($finalBuyingPrice - ($finalBuyingPrice * ($product->discount / 100)));
            }
            
            // Save the updated product's selling price
            $product->save();
            
            // Now, update the variant_mrp and selling_price for all the variants of this product
            $productVariants = ProductVariantCombination::where('product_id', $product->id)->get();
            
            foreach ($productVariants as $variant) {
                // Set variant_mrp and selling_price based on the product's updated prices
                $variant->variant_mrp = $finalBuyingPrice;  // Set variant_mrp to the updated buying_price
                $variant->selling_price = $product->selling_price;  // Set selling_price to the product's selling_price
                
                // Save the updated variant's prices
                $variant->save();
            }
        }
        
        // Redirect back with success message
        return redirect()->route('admin-price-drops.index')->with('success', 'Price change applied successfully.');
    }
    
    
    public function update(Request $request, $enuserid = null)
    {

        $model = PriceDrop::find($enuserid);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {

                $validator = Validator::make(
                    $request->all(),
                    array(
                        'drop_type' => 'required',
                        'category_id' => 'required',
                        'gain_type' => 'required',
                        'amount' => 'required|numeric',
                        'start_date' => 'required|date|after_or_equal:today',
                        'end_date' => 'required|date|after_or_equal:start_date',
                    ),
                );
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                } else {
                    // dd($request->product_id);
                    $assign_type = 'category';
                    if (!empty($request->product_id)) {
                        $assign_type = 'product';
                    }
                    DB::beginTransaction();

                    $obj                      = $model;
                    $obj->assign_type                   = $assign_type;
                    $obj->drop_type                     = $request->input('drop_type');
                    $obj->gain_type                     = $request->input('gain_type');
                    $obj->amount                        = !empty($request->input('amount')) ? $request->input('amount') : 0;
                    $obj->start_date                    = !empty($request->input('start_date')) ? $request->input('start_date') : null;
                    $obj->end_date                      = !empty($request->input('end_date')) ? $request->input('end_date') : null;
                    $obj->save();
                    $lastId = $obj->id;
                    if (!empty($lastId)) {
                        $category_id = $request->category_id;
                        if (!empty($request->sub_category_id)) {
                            $category_id = $request->sub_category_id;
                        }
                        if (!empty($request->child_category_id)) {
                            $category_id = $request->child_category_id;
                        }
                        if ($assign_type == "category" && !empty($category_id)) {
                            PriceDropAssign::where('price_drop_id', $lastId)->delete();
                            $obj1 = new PriceDropAssign;
                            $obj1->price_drop_id = $lastId;
                            $obj1->reference_id = $category_id;
                            $obj1->save();
                        } elseif ($assign_type == "product" && !empty($request->Product_id)) {
                            PriceDropAssign::where('price_drop_id', $lastId)->delete();
                            foreach ($request->Product_id as $pro_data) {
                                $obj1 = new PriceDropAssign;
                                $obj1->price_drop_id = $lastId;
                                $obj1->reference_id = $pro_data;
                                $obj1->save();
                            }
                        }
                        $log=new PriceDropLog;
                        $log->price_drop_id=$lastId;
                        $log->user_id=auth()->user()->id;
                        $log->action='Update';
                        $log->save();
                        DB::commit();
                    } else {
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return redirect()->route('admin-price-drops.index');
                    }
                    Session()->flash('flash_notice', trans("Price Drop updated successfully."));
                    return redirect()->route('admin-price-drops.index');
                }
            }
        }
    }

    public function destroy($enuserid){
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id     = base64_decode($enuserid);
        }
        $userDetails     =   PriceDrop::find($user_id);

       // dd($user_id);
        if (empty($userDetails)) {
            return redirect()->route('admin-price-drops.index');
        }
        if ($user_id) {
            PriceDrop::where('id', $user_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', trans("Price Drop has been removed successfully"));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Currency has been actvated successfully");
        } else {
            $statusMessage = trans("Currency has been deactivated successfully");
        }
        $user = PriceDrop::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }


    public function getPriceData()
    {
        $data = getDropPrices('52', 'selling', 'yes');
    }
}
