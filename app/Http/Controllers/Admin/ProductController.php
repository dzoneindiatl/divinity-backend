<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Tag;
use App\Models\User;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\UserReview;
use App\Models\SubCategory;
use App\Models\ProductImage;
use App\Models\VariantValue;
use Illuminate\Http\Request;
use App\Models\ChildCategory;
use App\Models\ProductValues;
use App\Models\AttributeValue;
use App\Models\ProductOptions;
use App\Models\ProductVariant;
use App\Models\CategoryVariant;
use App\Models\ProductVariants;
use App\Models\RefundedHistory;
use App\Models\ProductAttribute;
use App\Models\ProductCollection;
use App\Models\ProductColorImage;
use App\Models\{ProductDescription, CategoryAttribute};
use App\Models\SimpleVeriantValue;
use App\Models\SpecificationValue;
use App\Service\FileUploadService;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariantValue;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductSpecification;
use App\Models\CategorySpecification;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\Storage;
use App\Models\OptionValueProductVariant;
use App\Models\ProductVariantCombination;
use App\Models\VariantProductCombination;
use App\Models\ProductShippingSpecification;
use Intervention\Image\Laravel\Facades\Image;
#use Intervention\Image\ImageManagerStatic as Image;
use App\Models\ProductVariantCombinationImage;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Validator, Response, Redirect, Str, View, File;

class ProductController extends Controller
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

    public function updatedata(Request $request)
    {
        $productIds = array_keys($request->input('product', []));

        if (empty($request->bulk_action) || empty($productIds)) {
            redirect()->back();
        }

        if (!empty($productIds)) {
            switch ($request->input('bulk_action')) {
                case 1:
                    Product::whereIn('id', $productIds)->update(['is_active' => 2]);
                    break;

                case 2:
                    Product::whereIn('id', $productIds)->update(['is_active' => 1]);
                    break;

                case 3:
                    Product::whereIn('id', $productIds)->update(['is_active' => 3]);
                    break;

                case 4:
                    Product::whereIn('id', $productIds)->update(['is_featured' => 1]);
                    break;

                case 5:
                    Product::whereIn('id', $productIds)->update(['is_featured' => 0]);
                    break;

                case 6:
                    Product::whereIn('id', $productIds)->update(['is_new_arrivals' => 1]);
                    break;

                case 7:
                    Product::whereIn('id', $productIds)->update(['is_new_arrivals' => 0]);
                    break;

                default:

                    return redirect()->back();
            }
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function updateOrder(Request $request)
    {
        $products = $request->input('products');
        foreach ($products as $order => $id) {
            $product = Product::find($id);
            $product->position = $order;
            $product->save();
        }
        return response()->json('Order updated successfully', 200);
    }

    public function indexold(Request $request)
    {
        session()->forget('varient_product_image');
        session()->forget('product_images');
        try {
            $DB = Product::where('products.parent_id', 0); //->where('id',495);

            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'products.created_at';
            $order = $request->input('order') ? $request->input('order') : 'desc';
            $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
            $limit = !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

            if ($request->all()) {
                $searchData = $request->all();
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
                    $DB->whereBetween('products.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('products.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('products.created_at', '<=', [$dateE . " 00:00:00"]);
                }

                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "name") {
                            $DB->where("products.name", 'like', '%' . $fieldValue . '%');
                        }
                        if ($fieldName == "slug") {
                            $DB->where("products.slug", 'like', '%' . $fieldValue . '%');
                        }

                        if ($fieldName == "category_id") {

                            $DB->where("products.category_id", $fieldValue);
                        }
                        if ($fieldName == "is_active") {

                            $DB->where("products.is_active", $fieldValue);
                        }

                        // if ($fieldName == "sub_category_id") {
                        //     $DB->where("products.sub_category_id", $fieldValue);
                        // }
                        // if ($fieldName == "child_category_id") {
                        //     $DB->where("products.child_category_id", $fieldValue);
                        // }
                    }
                }
            }
            $results = $DB->with('frontProductImage', 'category', 'category.parentcategory')->select('products.*')->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

            $totalResults = $DB->count();
            if ($request->ajax()) {
                return View("admin.products.load_more_data", compact('results', 'totalResults'));
            } else {

                $categories = Category::whereNull('parent_id')->where('is_deleted', 0)->get();
                return view('admin.products.list', compact('results', 'categories', 'totalResults'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    /*public function index(Request $request)
    {
        //echo "<pre>@@@"; print_r($request->all()); exit;
       
        session()->forget('varient_product_image');
        session()->forget('product_images');
        try {
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'products.created_at';
            $order = $request->input('order') ? $request->input('order') : 'desc';
            $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
            $limit = !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

            $DB = Product::select('id', 'name', 'slug', 'buying_price', 'selling_price', 'category_id', 'sub_category_id', 'main_category_id', 'main_sub_category_id', 'in_stock', 'is_featured', 'is_active', 'draf', 'qty');

            if ($request->all()) {
                $searchData = $request->all();
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
                    $DB->whereBetween('products.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('products.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('products.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                if ($searchData['name']) {
                    $DB->where("name", 'like', '%' . $searchData['name'] . '%');
                }
                if ($searchData['category_id']) {
                    $DB->where("main_category_id", $searchData['category_id']);
                }
            }
            //$productLsit = $DB->with('frontProductImage', 'category','category.parentcategory')->where('is_deleted','0')->orderBy('id','DESC')->offset($offset)->limit(100)->get();

            $productLsit = $DB->with([
                'frontProductImage',
                'firstProductImage',
                'category',
                'subCategory',
                'mainCategory',
                'mainSubCategory',
                //'category.parentcategory'
            ])->withCount([
                'reviews as total_reviews',
                'reviews as new_reviews' => function ($query) {
                    $query->where('created_at', '>=', now()->subDays(7)); // Count reviews from the last 7 days
                }
            ])->where('is_deleted', '0')
                ->orderBy('id', 'DESC')
                ->offset($offset)
                ->limit(100)
                ->get();
               
            // echo "<pre>";
            // print_r($productLsit->toArray());die;
            // print_r($productLsit[0]->frontProductImage->graphic);
            // exit;

            //$productLsit->orderBy('id','DESC')-;
            //$productLsit = $productLsit->limit();

            $totalResults = $DB->count();
            if ($request->ajax()) {
                return View("admin.products.load_more_data", compact('productLsit', 'totalResults'));
            } else {
                
                $categories = Category::whereNull('parent_id')->where('is_deleted', 0)->get();
                return view('admin.products.list', compact('productLsit', 'categories', 'totalResults'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }*/
    public function index(Request $request)
    {
        //echo "<pre>@@@"; print_r($request->all()); exit;

        session()->forget('varient_product_image');
        session()->forget('product_images');
        try {
            $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'products.created_at';
            $order = $request->input('order') ? $request->input('order') : 'desc';
            $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
            $limit = !empty($request->input('limit')) ? $request->input('limit') : Config("Referral.receiver");
            $DB = Product::select('id', 'name', 'sku', 'short_description', 'slug', 'buying_price', 'selling_price', 'category_id', 'sub_category_id', 'main_category_id', 'main_sub_category_id', 'in_stock', 'is_featured', 'is_new_arrivals', 'is_new', 'trending', 'best_selling', 'best_seller', 'is_active', 'draf', 'qty');

            if ($request->all()) {
                $searchData = $request->all();
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
                    $DB->whereBetween('products.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('products.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('products.created_at', '<=', [$dateE . " 00:00:00"]);
                }
                if (isset($searchData['name']) && !empty($searchData['name'])) {
                    $DB->where("name", 'like', '%' . $searchData['name'] . '%');
                }
                if (isset($searchData['sku']) && !empty($searchData['sku'])) {
                    $DB->where("sku", 'like', '%' . $searchData['sku'] . '%');
                }
                if (isset($searchData['category_id']) && !empty($searchData['category_id'])) {
                    $DB->where("main_category_id", $searchData['category_id']);
                }
            }
            //$productLsit = $DB->with('frontProductImage', 'category','category.parentcategory')->where('is_deleted','0')->orderBy('id','DESC')->offset($offset)->limit(100)->get();
            $totalResults = $DB->count();

            $productLsit = $DB->with([
                'frontProductImage',
                'firstProductImage',
                'category',
                'subCategory',
                'mainCategory',
                'mainSubCategory',
                //'category.parentcategory'
            ])->withCount([
                'reviews as total_reviews',
                'reviews as new_reviews' => function ($query) {
                    $query->where('created_at', '>=', now()->subDays(7)); // Count reviews from the last 7 days
                }
            ])->where('is_deleted', '0')
                // ->orderBy('id', 'DESC')
                // //->offset($offset)
                // //->limit($limit)
                // ->get();

            ->orderBy('product_order', 'ASC')->paginate($limit)->appends(request()->query());

            // echo "<pre>";
            // print_r($productLsit->toArray());die;
            // print_r($productLsit[0]->frontProductImage->graphic);
            // exit;

            //$productLsit->orderBy('id','DESC')-;
            //$productLsit = $productLsit->limit();

            if ($request->ajax()) {
                return response()->json([
                    'html' => view("admin.products.load_more_data", compact('productLsit', 'totalResults', 'limit', 'offset'))->render(),
                    'totalResults' => $totalResults,
                ]);
            } else {

                $categories = Category::whereNull('parent_id')->where('is_deleted', 0)->get();
                return view('admin.products.list', compact('productLsit', 'categories', 'totalResults', 'limit', 'offset'));
            }
        } catch (Exception $e) {
           Log::error($e);
            return redirect()->back()->with(['error' => $e->getMessage(), 'error_msg' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {

            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();
            $brands = Brand::where('is_active', 1)->where('is_deleted', 0)->get();
            $producttags = Tag::where('is_active', 1)->where('is_deleted', 0)->get();


            if ($request->session()->has('currentProductId')) {
                $request->session()->forget('currentProductId');
            }
            $type = 'create';
            return view('admin.products.create', compact('categories', 'brands', 'type', 'producttags'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function view($token)
    {
        $prdId = decrypt($token);
        try {

            $result = [];
            $colorArray = [];
            $productVariants = ProductVariantCombination::where('product_id', $prdId)->get();

            // if(count($productVariants) > 0) {
            //     foreach($productVariants as $var){
            //         $colorArray[] = $var['color_variant_value_id'];
            //     }
            //     if(count($colorArray) > 0) {
            //         $arrUnique = array_unique($colorArray);
            //         if(count($arrUnique) > 0) {
            //             foreach($productVariants as $value){

            //                 if(in_array($value['color_variant_value_id'], $arrUnique)){
            //                     $result[$value['color_variant_value_id']] = [
            //                                                                     //'size' => [$value['size_variant_value_id']],
            //                                                                     'mrp' => $value[''],
            //                                                                     'selling_price' => $value['selling_price'],
            //                                                                     'units' => $value['available_units'],
            //                                                                     'discount' => $value['0.00'],
            //                                                                     'sku' => $value['sku'],
            //                                                                     'color' => 'orange'
            //                                                                 ];
            //                 }

            //             }
            //         }
            //     }
            // }
            return view('admin.products.view', compact('productVariants', 'result'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function generateProductNumber($lastId = 0)
    {
        if (!empty($lastId)) {
            $productNumber = "JJ-" . ($lastId + 10000);
            return $productNumber;
        }
    }

    public function store(Request $request)
    {

        $formData = $request->all();
        $response = array();
        if (!empty($formData)) {

            $basicInformationValidationArray = [
                'name' => 'required',
                // 'bar_code' => 'required',
                'category_id' => 'required',
                // 'brand_id' => 'required'
            ];

            $detailsValidationArray = [
                // 'long_description' => 'required',
            ];
            $pricesValidationArray = [
                'buying_price' => 'required|numeric|gt:0',
                'selling_price' => 'required|numeric|gt:0',
            ];
            $specificationsValidationArray = [
                // 'specificationDataArr' => 'required|array|at_least_one_value'
            ];
            $shippingSpecificationsValidationArray = [
                // 'height' => 'required',
                // 'weight' => 'required',
                // 'width' => 'required',
                // 'length' => 'required',
                // 'dc' => 'required'
            ];
            $variantsTabFirstStepArray = [
                // 'variantsDataArr' => 'required|array|at_least_one_value_variant'
            ];
            $variantsTabSecondStepArray = [
                // 'variantsDataArr' => 'required|array|at_least_one_value_variant',
                // 'variantCombinationArr' => 'required|array'
            ];
            $mediasValidationArray = [];
            $advanceSeoValidationArray = [];
            Validator::extend('at_least_one_value', function ($attribute, $value, $parameters, $validator) {
                foreach ($value as $item) {
                    if (!empty($item['specification_values'][0])) {
                        return true;
                    }
                }
                return false;
            });
            Validator::extend('at_least_one_value_variant', function ($attribute, $value, $parameters, $validator) {
                foreach ($value as $item) {
                    if (!empty($item['variant_values'][0]) && !empty($item['variant_id'])) {
                        return true;
                    }
                }
                return false;
            });



            $validator = Validator::make(
                $request->all(),
                (!empty($request->current_tab) && $request->current_tab == 'basicInformationTab') ? $basicInformationValidationArray : ((!empty($request->current_tab) && $request->current_tab == 'detailsTab') ? $detailsValidationArray : ((!empty($request->current_tab) && $request->current_tab == 'pricesTab') ? $pricesValidationArray : ((!empty($request->current_tab) && $request->current_tab == 'specificationsTab') ? $specificationsValidationArray : ((!empty($request->current_tab) && $request->current_tab == 'shippingSpecificationsTab') ? $shippingSpecificationsValidationArray : ((!empty($request->current_tab) && $request->current_tab == 'mediasTab') ? $mediasValidationArray : ((!empty($request->current_tab) && $request->current_tab == 'variantsTab' && !empty($request->current_action) && $request->current_action == 'first_step') ? $variantsTabFirstStepArray : ((!empty($request->current_tab) && $request->current_tab == 'variantsTab' && !empty($request->current_action) && $request->current_action == 'second_step') ? $variantsTabSecondStepArray : ((!empty($request->current_tab) && $request->current_tab == 'advanceSeoTab') ? $advanceSeoValidationArray : [])))))))),

                array(
                    "specificationDataArr.required" => trans("The specifications fields are required."),
                    "specificationDataArr.array" => trans("The specifications should be array."),
                    "specificationDataArr.at_least_one_value" => trans("Please select atleast one values."),
                    "variantsDataArr.at_least_one_value_variant" => trans("Please select atleast one variant and its values. "),
                )
            );

            if ($validator->fails()) {
                $response = $this->change_error_msg_layout($validator->errors()->getMessages());
                return Response::json($response, 200);
            } else {

                if ((!empty($request->current_tab) && $request->current_tab == 'basicInformationTab')) {

                    if (!empty($request->session()->has('currentProductId'))) {
                        $obj = Product::find($request->session()->get('currentProductId'));
                    } else {
                        $obj = new Product;
                    }
                    $originalString = $request->name ?? "";
                    $lowercaseString = Str::lower($originalString);
                    $slug = Str::slug($lowercaseString, '-');

                    if ((!empty($request->session()->has('currentProductId')) && ($obj->name != $request->name)) || (empty($request->session()->has('currentProductId')))) {
                        $alreadyAddedName = Product::where('name', $originalString)->first();

                        if (!is_null($alreadyAddedName)) {
                            $response = array();
                            $response["status"] = "error";
                            $response["msg"] = trans("Slug is already added");
                            $response["data"] = (object) array();
                            $response["http_code"] = 500;
                            return Response::json($response, 500);
                        }
                    }

                    $obj->slug = $slug;

                    $obj->name = !empty($request->name) ? $request->name : NULL;;
                    $obj->product_number = '00';
                    $obj->bar_code = $request->bar_code ?? Null;
                    $obj->category_id = $request->category_id;
                    $obj->brand_id = $request->brand_id ?? Null;
                    $obj->sub_category_id = !empty($request->sub_category_id) ? $request->sub_category_id : NULL;
                    $obj->child_category_id = !empty($request->child_category_id) ? $request->child_category_id : NULL;
                    $obj->draf = false;
                    $obj->is_active = true;
                    $obj->save();
                    $lastId = $obj->id;
                    if (empty($lastId)) {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("Something Went Wrong");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    } else {
                        // Putting data into session
                        $request->session()->put('currentProductId', $lastId);

                        //Update Product Number
                        Product::where('id', $lastId)->update(['product_number' => $this->generateProductNumber($lastId)]);

                        $response = array();
                        $response["status"] = "success";
                        $response["msg"] = "";
                        $response["data"] = (object) array();
                        $response["http_code"] = 200;
                        return Response::json($response, 200);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'detailsTab')) {
                    if (!empty($request->session()->has('currentProductId'))) {
                        ProductDescription::where('product_id', $request->session()->get('currentProductId'))->delete();
                        if (!empty($request->productDetailsArr)) {
                            foreach ($request->productDetailsArr as $productDetailKey => $productDetail) {
                                if (!empty($productDetail['name']) && !empty($productDetail['value'])) {

                                    $obj = new ProductDescription;
                                    $obj->product_id = $request->session()->get('currentProductId');
                                    $obj->name = $productDetail['name'];
                                    $obj->value = $productDetail['value'];
                                    $obj->save();
                                    $lastDetailId = $obj->id;
                                    if (empty($lastDetailId)) {
                                        $response = array();
                                        $response["status"] = "error";
                                        $response["msg"] = trans("Something Went Wrong");
                                        $response["data"] = (object) array();
                                        $response["http_code"] = 500;
                                        return Response::json($response, 500);
                                    }
                                }
                            }
                        }
                        $response = array();
                        $response["status"] = "success";
                        $response["msg"] = "";
                        $response["data"] = (object) array();
                        $response["http_code"] = 200;
                        return Response::json($response, 200);
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("messages.Invalid_Request");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'pricesTab')) {
                    if (!empty($request->session()->has('currentProductId'))) {
                        $obj = Product::find($request->session()->get('currentProductId'));
                        $obj->buying_price = !empty($request->buying_price) ? $request->buying_price : '0.00';
                        $obj->selling_price = !empty($request->selling_price) ? $request->selling_price : '0.00';
                        $obj->is_including_taxes = !empty($request->is_including_taxes) ? 1 : 0;

                        $obj->save();
                        $lastId = $obj->id;
                        if (empty($lastId)) {
                            $response = array();
                            $response["status"] = "error";
                            $response["msg"] = trans("Something Went Wrong");
                            $response["data"] = (object) array();
                            $response["http_code"] = 500;
                            return Response::json($response, 500);
                        } else {
                            $productData = Product::where('id', $lastId)->first();

                            $specificationsData = CategorySpecification::select('specifications.id', 'specifications.name')
                                ->leftJoin('specifications', 'category_specifications.specification_id', '=', 'specifications.id')
                                ->where(function ($query) use ($productData) {
                                    $query->where('category_specifications.category_id', $productData->category_id)
                                        ->orWhere('category_specifications.category_id', $productData->sub_category_id)
                                        ->orWhere('category_specifications.category_id', $productData->child_category_id);
                                })
                                ->distinct()
                                ->get()->toArray();
                            if (!empty($specificationsData)) {
                                foreach ($specificationsData as &$dataVal) {
                                    $dataVal['specification_values'] = SpecificationValue::where('specification_id', $dataVal['id'])->get()->toArray();
                                }
                            }
                            $productSpecifications = ProductSpecification::where('product_id', $request->session()->get('currentProductId'))->pluck('specification_value_id')->toArray();
                            $htmlData = View::make("admin.products.specifications_data", compact('specificationsData', 'productSpecifications'))->render();

                            $response = array();
                            $response["status"] = "success";
                            $response["msg"] = "";
                            $response["data"] = $htmlData;
                            $response["http_code"] = 200;
                            return Response::json($response, 200);
                        }
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("messages.Invalid_Request");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'specificationsTab')) {
                    if (!empty($request->session()->has('currentProductId'))) {

                        if (!empty($request->specificationDataArr)) {
                            ProductSpecification::where('product_id', $request->session()->get('currentProductId'))->delete();
                            foreach ($request->specificationDataArr as $specValue) {
                                if (!empty($specValue['name']) && !empty($specValue['specification_id']) && !empty($specValue['specification_values'][0])) {
                                    foreach ($specValue['specification_values'] as $dataVal) {
                                        $obj2 = new ProductSpecification;
                                        $obj2->product_id = $request->session()->get('currentProductId');
                                        $obj2->specification_id = $specValue['specification_id'];
                                        $obj2->specification_value_id = $dataVal;
                                        $obj2->save();
                                    }
                                }
                            }
                            $response = array();
                            $response["status"] = "success";
                            $response["msg"] = "";
                            $response["data"] = (object) array();
                            $response["http_code"] = 200;
                            return Response::json($response, 200);
                        }/* else {
                            $response = array();
                            $response["status"] = "error";
                            $response["msg"] = trans("Something went wrong");
                            $response["data"] = (object) array();
                            $response["http_code"] = 500;
                            return Response::json($response, 500);
                        }*/
                        $response = array();
                        $response["status"] = "success";
                        $response["msg"] = "";
                        $response["data"] = (object) array();
                        $response["http_code"] = 200;
                        return Response::json($response, 200);
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("messages.Invalid_Request");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'shippingSpecificationsTab')) {
                    if (!empty($request->session()->has('currentProductId'))) {
                        $shippingSpecifications = ProductShippingSpecification::where('product_id', $request->session()->get('currentProductId'))->first();
                        if (!empty($shippingSpecifications)) {
                            $obj = ProductShippingSpecification::find($shippingSpecifications->id);
                        } else {
                            $obj = new ProductShippingSpecification;
                        }

                        $obj->product_id = $request->session()->get('currentProductId');
                        $obj->height = !empty($request->height) ? $request->height : NULL;
                        $obj->weight = !empty($request->weight) ? $request->weight : NULL;
                        $obj->width = !empty($request->width) ? $request->width : NULL;
                        $obj->length = !empty($request->length) ? $request->length : NULL;
                        $obj->dc = !empty($request->dc) ? $request->dc : NULL;
                        $obj->save();
                        $lastId = $obj->id;
                        if (empty($lastId)) {
                            $response = array();
                            $response["status"] = "error";
                            $response["msg"] = trans("Something Went Wrong");
                            $response["data"] = (object) array();
                            $response["http_code"] = 500;
                            return Response::json($response, 500);
                        } else {

                            $response = array();
                            $response["status"] = "success";
                            $response["msg"] = "";
                            $response["data"] = (object) array();
                            $response["http_code"] = 200;
                            return Response::json($response, 200);
                        }
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("messages.Invalid_Request");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'mediasTab')) {
                    if (!empty($request->session()->has('currentProductId'))) {
                        $checkIfAnyMediaExists = ProductImage::where('product_id', $request->session()->get('currentProductId'))->count();
                        if ($checkIfAnyMediaExists == 0) {
                            $response = array();
                            $response["status"] = "error";
                            $response["msg"] = trans("Please upload atleast one image to continue.");
                            $response["data"] = (object) array();
                            $response["http_code"] = 500;
                            return Response::json($response, 500);
                        } else {
                            $productData = Product::where('id', $request->session()->get('currentProductId'))->first();

                            $variantsData = CategoryVariant::select('variants.id', 'variants.name')
                                ->leftJoin('variants', 'category_variants.variant_id', '=', 'variants.id')
                                ->where(function ($query) use ($productData) {
                                    $query->where('category_variants.category_id', $productData->category_id)
                                        ->orWhere('category_variants.category_id', $productData->sub_category_id)
                                        ->orWhere('category_variants.category_id', $productData->child_category_id);
                                })
                                ->distinct()
                                ->get()->toArray();

                            $productVariants = ProductVariant::where('product_id', $request->session()->get('currentProductId'))->pluck('variant_id')->toArray();
                            $hasProductVariantCombinationsOtherThanMainProductOnEditPage = ProductVariantCombination::where('product_id', $request->session()->get('currentProductId'))->where('is_main_product', 0)->count();
                            // print_r($productVariants);die;

                            $htmlData = View::make("admin.products.variants_data", compact('variantsData', 'productVariants', 'hasProductVariantCombinationsOtherThanMainProductOnEditPage'))->render();
                            $response = array();
                            $response["status"] = "success";
                            $response["msg"] = "";
                            $response["data"] = $htmlData;
                            $response["http_code"] = 200;
                            return Response::json($response, 200);
                        }
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("messages.Invalid_Request");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'variantsTab' && $request->current_action == 'first_step')) {

                    if (!empty($request->session()->has('currentProductId'))) {
                        $productDetails = Product::where('id', $request->session()->get('currentProductId'))->first();

                        $response =  $this->variantsFirstStepAction($request, $productDetails);
                        return $response;
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("messages.Invalid_Request");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'variantsTab' && $request->current_action == 'second_step')) {

                    if (!empty($request->session()->has('currentProductId'))) {
                        $productDetails = Product::where('id', $request->session()->get('currentProductId'))->first();

                        $this->variantsFirstStepAction($request, $productDetails);

                        $variantCombinationArr = $request->variantCombinationArr;

                        if (!empty($variantCombinationArr)) {

                            $getProductVariantCombinations = ProductVariantCombination::where('product_id', $request->session()->get('currentProductId'))->get();
                            if ($getProductVariantCombinations->isNotEmpty()) {
                                foreach ($getProductVariantCombinations as $pVarValue) {
                                    ProductVariantCombinationImage::where('product_variant_combination_id', $pVarValue->id)->delete();
                                }
                            }
                            ProductVariantCombination::where('product_id', $request->session()->get('currentProductId'))->delete();

                            DB::beginTransaction();
                            foreach ($variantCombinationArr as $variantCombVal) {
                                if (!empty($variantCombVal['image_ids']) && !empty($variantCombVal['main_variant_id']) && !empty($variantCombVal['variant_value_ids'])) {
                                    foreach ($variantCombVal['variant_value_ids'] as $variantValIdData) {
                                        if (!empty($variantValIdData['buying_price']) && !empty($variantValIdData['selling_price'])) {
                                            $getVariantValueName = VariantValue::where('id', $variantCombVal['main_variant_id'])->first()->name;
                                            $originalString = $getVariantValueName ?? "";
                                            $lowercaseString = Str::lower($originalString);
                                            $slug = Str::slug($lowercaseString, '-');

                                            $obj = new ProductVariantCombination;
                                            if (!empty($variantValIdData['value_id'])) {

                                                $getVariantValueName2 = VariantValue::where('id', $variantValIdData['value_id'])->first()->name;
                                                $originalString2 = $getVariantValueName2 ?? "";
                                                $lowercaseString2 = Str::lower($originalString2);
                                                $slug2 = Str::slug($lowercaseString2, '-');
                                                $obj->slug = $productDetails->slug . "-" . $slug . "-" . $slug2;
                                            } else {

                                                $obj->slug = $productDetails->slug . "-" . $slug;
                                            }
                                            $obj->product_id = $productDetails->id;
                                            $obj->variant1_value_id = $variantCombVal['main_variant_id'];
                                            $obj->variant2_value_id = !empty($variantValIdData['value_id']) ? $variantValIdData['value_id'] : NULL;
                                            $obj->buying_price = $variantValIdData['buying_price'] ?? 0.00;
                                            $obj->selling_price = $variantValIdData['selling_price'] ?? 0.00;
                                            $obj->height = $variantValIdData['height'] ?? Null;
                                            $obj->weight = $variantValIdData['weight'] ?? Null;
                                            $obj->width = $variantValIdData['width'] ?? Null;
                                            $obj->length = $variantValIdData['length'] ?? Null;
                                            $obj->dc = $variantValIdData['dc'] ?? Null;
                                            $obj->bar_code = $variantValIdData['bar_code'] ?? Null;
                                            $obj->product_number = ' ';
                                            $obj->save();
                                            $lastId = $obj->id;
                                            if (empty($lastId)) {
                                                DB::rollback();
                                                $response = array();
                                                $response["status"] = "error";
                                                $response["msg"] = trans("Something Went Wrong");
                                                $response["data"] = (object) array();
                                                $response["http_code"] = 500;
                                                return Response::json($response, 500);
                                            }
                                            ProductVariantCombination::where('id', $lastId)->update(['product_number' => $productDetails->product_number . 'V' . $lastId]);

                                            foreach ($variantCombVal['image_ids'] as $imageVal) {
                                                $obj2 = new ProductVariantCombinationImage;
                                                $obj2->product_variant_combination_id = $lastId;
                                                $obj2->product_image_id = $imageVal;
                                                $obj2->save();
                                                if (empty($obj2->id)) {
                                                    DB::rollback();
                                                    $response = array();
                                                    $response["status"] = "error";
                                                    $response["msg"] = trans("Something Went Wrong");
                                                    $response["data"] = (object) array();
                                                    $response["http_code"] = 500;
                                                    return Response::json($response, 500);
                                                }
                                            }
                                        } else {
                                            DB::rollback();
                                            $response = array();
                                            $response["status"] = "error";
                                            $response["msg"] = trans("Please fill all the details before submitting the combinations.");
                                            $response["data"] = (object) array();
                                            $response["http_code"] = 500;
                                            return Response::json($response, 500);
                                        }
                                    }
                                } else {
                                    DB::rollback();
                                    $response = array();
                                    $response["status"] = "error";
                                    $response["msg"] = trans("Please fill all the details before submitting the combinations.");
                                    $response["data"] = (object) array();
                                    $response["http_code"] = 500;
                                    return Response::json($response, 500);
                                }
                            }
                            DB::commit();
                        }


                        $response = array();
                        $response["status"] = "success";
                        $response["msg"] = "";
                        $response["data"] = (object) array();
                        $response["http_code"] = 200;
                        return Response::json($response, 200);
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("Something Went Wrong");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else if ((!empty($request->current_tab) && $request->current_tab == 'advanceSeoTab')) {
                    if (!empty($request->session()->has('currentProductId'))) {
                        $obj = Product::find($request->session()->get('currentProductId'));
                        $obj->meta_title = !empty($request->meta_title) ? $request->meta_title : '';
                        $obj->meta_description = !empty($request->meta_description) ? $request->meta_description : '';
                        $obj->meta_keywords = !empty($request->meta_keywords) ? $request->meta_keywords : '';

                        $obj->save();
                        $lastId = $obj->id;
                        if (empty($lastId)) {
                            $response = array();
                            $response["status"] = "error";
                            $response["msg"] = trans("Something Went Wrong");
                            $response["data"] = (object) array();
                            $response["http_code"] = 500;
                            return Response::json($response, 500);
                        } else {
                            $request->session()->forget('currentProductId');

                            $response = array();
                            $response["status"] = "success";
                            $response["msg"] = "Product added successfully.";
                            $response["data"] = (object) array();
                            $response["http_code"] = 200;
                            return Response::json($response, 200);
                        }
                    } else {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("messages.Invalid_Request");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                } else {
                    $response = array();
                    $response["status"] = "error";
                    $response["msg"] = trans("messages.Invalid_Request");
                    $response["data"] = (object) array();
                    $response["http_code"] = 500;
                    return Response::json($response, 500);
                }
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("messages.Invalid_Request");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
        return json_encode($response);
    }

    function variantsFirstStepAction($request, $productDetails)
    {
        $variantsDataArr = $request->variantsDataArr;
        if (!empty($variantsDataArr)) {

            $getProductVariants = ProductVariant::where('product_id', $request->session()->get('currentProductId'))->get();
            if ($getProductVariants->isNotEmpty()) {
                foreach ($getProductVariants as $pVarValue) {
                    ProductVariantValue::where('product_veriant_id', $pVarValue->id)->delete();
                }
            }
            ProductVariant::where('product_id', $request->session()->get('currentProductId'))->delete();

            foreach ($variantsDataArr as $variantValue) {
                if (!empty($variantValue['variant_id']) && !empty($variantValue['variant_values'][0])) {
                    $obj2 = new ProductVariant;
                    $obj2->product_id = $request->session()->get('currentProductId');
                    $obj2->variant_id = $variantValue['variant_id'];
                    $obj2->save();
                    $pVariantId = $obj2->id;
                    if (empty($pVariantId)) {
                        $response = array();
                        $response["status"] = "error";
                        $response["msg"] = trans("Something Went Wrong");
                        $response["data"] = (object) array();
                        $response["http_code"] = 500;
                        return Response::json($response, 500);
                    }
                    foreach ($variantValue['variant_values'] as $dataVal) {
                        $obj3 = new ProductVariantValue;
                        $obj3->product_veriant_id = $pVariantId;
                        $obj3->veriant_value_id = $dataVal;
                        $obj3->save();
                    }
                }
            }

            // Inserting main product entry in ProductVariantCombinations Table
            $checkIfMainProductCombinationExists = ProductVariantCombination::where('product_id', $productDetails->id)->where('is_main_product', 1)->first();
            if (!empty($checkIfMainProductCombinationExists)) {

                $obj =  ProductVariantCombination::find($checkIfMainProductCombinationExists->id);
            } else {

                $obj = new ProductVariantCombination;
            }
            $obj->is_main_product = 1;
            $obj->slug = $productDetails->slug ?? Null;
            $obj->product_id = $productDetails->id ?? Null;
            $obj->variant1_value_id = Null;
            $obj->variant2_value_id = Null;
            $obj->buying_price = $productDetails->buying_price ?? 0.00;
            $obj->selling_price = $productDetails->selling_price ?? 0.00;
            $obj->height = $productDetails->height ?? Null;
            $obj->weight = $productDetails->weight ?? Null;
            $obj->width = $productDetails->width ?? Null;
            $obj->length = $productDetails->length ?? Null;
            $obj->dc = $productDetails->dc ?? Null;
            $obj->bar_code = $productDetails->bar_code ?? Null;
            $obj->product_number = $productDetails->product_number ?? Null;
            $obj->save();
            $variantCombId = $obj->id;

            // Inserting main product Images entry in ProductVariantCombinationImages Table
            if (!empty($variantCombId)) {
                $productImages = ProductImage::where('product_id', $productDetails->id)->get();
                if ($productImages->isNotEmpty()) {
                    ProductVariantCombinationImage::where('product_variant_combination_id', $variantCombId)->delete();
                    foreach ($productImages as $productImageData) {
                        $obj = new ProductVariantCombinationImage;
                        $obj->product_variant_combination_id = $variantCombId;
                        $obj->product_image_id = $productImageData->id;
                        $obj->save();
                    }
                }
            }


            foreach ($variantsDataArr as $key => $variantData) {
                if (!empty($variantData['variant_id']) && !empty($variantData['variant_values'][0])) {
                    $variantName = $this->getVariantName($variantData['variant_id']);
                    $variantsDataArr[$key]['variant_name'] = $variantName;

                    $variantValuesNames = $this->getVariantValuesNames($variantData['variant_values']);
                    $variantValuesStoredData = $this->getVariantValuesStoredData($variantData['variant_values']);

                    // if(!empty($variantValuesStoredData)){
                    //     $selectedImages = ProductVariantCombinationImage::where('product_variant_combination_id',$variantValuesStoredData->id)->pluck('product_image_id')->toArray();
                    //     $variantsDataArr[$key]['selected_images'] = $selectedImages;
                    // }
                    $variantsDataArr[$key]['variant_values_names'] = $variantValuesNames;
                    $variantsDataArr[$key]['variant_values_data'] = $variantValuesStoredData;
                } else {
                    unset($variantsDataArr[$key]);
                }
            }

            $productImages = ProductImage::where('product_id', $request->session()->get('currentProductId'))->get();
            // if($productImages->isNotEmpty()){
            //     foreach ($productImages as $key => $productImage) {
            //         $checkIfItIsSelected = ProductVariantCombinationImage::where('product_variant_combination_id')
            //     }
            // }
            $htmlData = View::make("admin.products.variants_combinations", compact('variantsDataArr', 'productImages'))->render();

            $response = array();
            $response["status"] = "success";
            $response["msg"] = "";
            $response["data"] = $htmlData;
            $response["http_code"] = 200;
            return Response::json($response, 200);
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("Something Went Wrong");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }


    function getVariantName($variantId)
    {

        $variant = Variant::find($variantId); // Fetching variant data from the database
        return $variant->name; // Returning the variant name
    }

    function getVariantValuesNames($variantValues)
    {

        $variantValuesNames = VariantValue::whereIn('id', $variantValues)->pluck('name')->toArray();
        return $variantValuesNames; // Return the variant values names
    }

    function getVariantValuesStoredData($variantValues)
    {
        $returnData = [];
        if (!empty(request()->session()->has('currentProductId'))) {
            foreach ($variantValues as $variantValue) {
                $variantValueData = ProductVariantCombination::where('product_id', request()->session()->get('currentProductId'))->where(function ($query) use ($variantValue) {
                    $query->where('product_variant_combinations.variant1_value_id', $variantValue)
                        ->orWhere('product_variant_combinations.variant2_value_id', $variantValue);
                })->where('product_variant_combinations.is_main_product', 0)->first();
                if (!empty($variantValueData)) {

                    $returnData[] = $variantValueData;
                }
            }
        }
        return $returnData;
    }

    public function uploadImages(Request $request)
    {
        if (!empty($request->session()->has('currentProductId'))) {
            $productDetails = Product::where('id', $request->session()->get('currentProductId'))->first();
            if (!empty($productDetails)) {
                $formData = $request->all();
                if (!empty($formData)) {
                    $validator = Validator::make(
                        $request->all(),
                        array(

                            'file' => 'required',
                            'file.*' => 'required|mimes:jpg,jpeg,png',

                        ),
                        array(

                            "file.required" => trans("messages.The_image_field_is_required"),
                            "file.*.mimes" => trans("messages.The_images_must_be_a_file_of_type_jpg_jpeg_png"),

                        )
                    );
                    if ($validator->fails()) {
                        $response = $this->change_error_msg_layout($validator->errors()->getMessages());
                        return Response::json($response, 200);
                    } else {


                        $successMsg = 'Images added successfully';


                        if (!empty($request->file)) {
                            DB::beginTransaction();
                            $checkIfFrontBackImageExists = ProductImage::where('product_id', $productDetails->id)->where(function ($query) {

                                $query->where("product_images.is_front", 1);
                                $query->orWhere("product_images.is_back", 1);
                            })->count();
                            foreach ($request->file as $imageKey => $imageVal) {
                                if (!empty($imageVal)) {

                                    $obj = new ProductImage;
                                    $obj->product_id = $productDetails->id;

                                    $extension = $imageVal->getClientOriginalExtension();
                                    $originalName = $imageVal->getClientOriginalName();
                                    $fileName = time() . '-product_image-' . $productDetails->id . $imageKey . '.' . $extension;

                                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                                    $folderPath = Config('constant.PRODUCT_IMAGE_ROOT_PATH') . $folderName;
                                    if (!File::exists($folderPath)) {
                                        File::makeDirectory($folderPath, $mode = 0777, true);
                                    }
                                    if ($imageVal->move($folderPath, $fileName)) {
                                        $obj->image = $folderName . $fileName;
                                        // $obj->original_image_name = $originalName;
                                    }
                                    if (($checkIfFrontBackImageExists == 0) && ($imageKey == 0)) {
                                        $obj->is_front = 1;
                                        $obj->is_back = 1;
                                    }
                                    $obj->save();
                                    $lastId = $obj->id;
                                    if (empty($lastId)) {
                                        DB::rollback();
                                        $response = array();
                                        $response["status"] = "error";
                                        $response["msg"] = trans("Something went wrong");
                                        $response["data"] = (object) array();
                                        $response["http_code"] = 500;
                                        return Response::json($response, 500);
                                    }
                                }
                            }
                        }
                        DB::commit();

                        $getData = ProductImage::where('product_id', $productDetails->id)->orderBy('created_at', 'desc')->get();


                        $getData = View::make('admin.products.load_images', compact('getData'))->render();

                        $response = array();
                        $response["status"] = "success";
                        $response["msg"] = trans($successMsg);
                        $response["data"] = $getData;
                        $response["http_code"] = 200;
                        return Response::json($response, 200);
                    }
                } else {
                    $response = array();
                    $response["status"] = "error";
                    $response["msg"] = trans("Invalid Request");
                    $response["data"] = (object) array();
                    $response["http_code"] = 500;
                    return Response::json($response, 500);
                }
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("Something went wrong");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("Invalid Request");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function deleteImage(Request $request)
    {
        if (!empty($request->session()->has('currentProductId'))) {
            $productDetails = Product::where('id', $request->session()->get('currentProductId'))->first();
            if (!empty($productDetails)) {
                if (!empty($request->id)) {
                    $recordId = base64_decode($request->id);
                    $getRecordData = ProductImage::where('id', $recordId)->where('product_id', $productDetails->id)->first();
                    if (!empty($getRecordData)) {
                        $filePath = Config('constant.PRODUCT_IMAGE_ROOT_PATH') . $getRecordData->image;
                        if (File::exists($filePath)) {
                            File::delete($filePath);
                        }
                        ProductImage::where('id', $recordId)->where('product_id', $productDetails->id)->delete();
                    }

                    // $getData = ProductImage::where('product_id', $productDetails->id)->orderBy('created_at', 'desc')->get();

                    // $getData = View::make('admin.products.load_images', compact('getData'))->render();

                    $response = array();
                    $response["status"] = "success";
                    $response["msg"] = trans("messages.Image deleted successfully.");
                    $response["data"] = (object) array();
                    $response["http_code"] = 200;
                    return Response::json($response, 200);
                } else {
                    $response = array();
                    $response["status"] = "error";
                    $response["msg"] = trans("messages.Invalid_Request");
                    $response["data"] = (object) array();
                    $response["http_code"] = 500;
                    return Response::json($response, 500);
                }
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("messages.Something_went_wrong");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("messages.Invalid_Request");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function updateImageMetaValues(Request $request)
    {
        if (!empty($request->session()->has('currentProductId'))) {
            $productDetails = Product::where('id', $request->session()->get('currentProductId'))->first();
            if (!empty($productDetails)) {
                if (!empty($request->id) && !empty($request->type)) {
                    $recordId = base64_decode($request->id);
                    ProductImage::where('product_id', $productDetails->id)->update(['is_' . $request->type => 0]);
                    ProductImage::where('id', $recordId)->where('product_id', $productDetails->id)->update(['is_' . $request->type => 1]);

                    $response = array();
                    $response["status"] = "success";
                    $response["msg"] = trans("Updated successfully.");
                    $response["data"] = (object) array();
                    $response["http_code"] = 200;
                    return Response::json($response, 200);
                } else {
                    $response = array();
                    $response["status"] = "error";
                    $response["msg"] = trans("messages.Invalid_Request");
                    $response["data"] = (object) array();
                    $response["http_code"] = 500;
                    return Response::json($response, 500);
                }
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("messages.Something_went_wrong");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("messages.Invalid_Request");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function update(Request $request, $token)
    {
        try {
            #echo "<pre>";print_r($request->all());exit;
            $productId = decrypt($token);
            $product = Product::find($productId);
            $oldFrontImage = $product->front_image;
            $oldBackImage = $product->back_image;
            $oldProductsImages = $product->images;
            $oldProductsVideos = $product->videos;

            $frontSelectedFile = $request->front_image ?? "";
            $backSelectedFile = $request->back_image ?? "";
            $editProductImageFiles = $request->edit_product_images ?? "";
            $productVideosFiles = $request->product_videos ?? "";

            if ($frontSelectedFile) {
                $frontImage = $request->file('front_image');
                $frontImagePath = "products/variants/front-images";
                $frontUploadedFile = $this->fileUploadService->uploadFile($frontImage, $frontImagePath);
            }

            if ($backSelectedFile) {
                $backImage = $request->file('back_image');
                $backImagePath = "products/variants/back-images";
                $backUploadedFile = $this->fileUploadService->uploadFile($backImage, $backImagePath);
            }

            if ($editProductImageFiles) {
                foreach ($request->file('edit_product_images') as $editProductImage) {
                    $productImagePath = "products/variants/images";
                    $editProductImagesUploadedFile[] = $this->fileUploadService->uploadFile($editProductImage, $productImagePath);
                }
            }

            if ($productVideosFiles) {
                foreach ($request->file('product_videos') as $productVideo) {
                    $productVideoPath = "products/variants/videos";
                    $editProductVideosUploadedFiles[] = $this->fileUploadService->uploadFile($productVideo, $productVideoPath);
                }
            }

            $product = tap($product)->update([

                'name' => $request->name,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id ?? null,
                'child_category_id' => $request->child_category_id ?? null,

            ]);

            // $productVariant = ProductVariants::where('product_id',$product->id)->first();
            $productVariant = ProductVariants::create([
                'product_id' => $product,
                'short_description' => $request->short_description,
                'description' => $request->description ?? null,
                'front_image' => $frontUploadedFile ?? $oldFrontImage,
                'back_image' => $backUploadedFile ?? $oldBackImage,
                'images' => json_encode($editProductImagesUploadedFile) ?? $oldProductsImages,
                'videos' => json_encode($editProductVideosUploadedFiles) ?? null,
                'price' => $request->price,
                'meta_title' => $request->meta_title ?? null,
                'meta_description' => $request->meta_description ?? null,
                'meta_keywords' => $request->meta_keywords ?? null,
            ]);
            return redirect()->route('admin-product-list')->with('success', 'Product updated successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function destory($token)
    {

        try {
            $productId = decrypt($token);
            $product = Product::find($productId);
            $product->delete();
            return redirect()->route('admin-product-list')->with('success', 'Product deleted successfully');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function getSubCategories(Request $request)
    {

        $parentId = $request->parent_id;
        $subcategories = Category::where('parent_id', $parentId)->get();

        return response()->json($subcategories);
    }

    public function getVariants(Request $request)
    {
        try {
            if (!empty($request)) {
                $variantValues = Variant::select('id', 'name')->where('is_active', 1)->where('is_deleted', 0)->get();

                return response()->json(['variants' => $variantValues, 'success' => true, 'message' => 'Data fetched'], 200);
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("messages.Invalid_Request");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['message' => 'Something is wrong', 'success' => false, 'error_msg' => $e->getMessage()], 500);
        }
    }

    public function getVariantValues(Request $request)
    {
        try {
            if (!empty($request->session()->has('currentProductId'))) {
                $variantId = $request->variant_id ?? "";
                $variantValues = VariantValue::where('variant_id', $variantId)->where('is_deleted', 0)->get();
                $productVariant = ProductVariant::where('variant_id', $variantId)->where('product_id', $request->session()->get('currentProductId'))->first();
                $productVariantValues = [];
                if (!empty($productVariant)) {

                    $productVariantValues = ProductVariantValue::where('product_veriant_id', $productVariant->id)->pluck('veriant_value_id');
                }

                return response()->json(['variantValues' => $variantValues, 'productVariantValues' => $productVariantValues, 'success' => true, 'message' => 'Data fetched'], 200);
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("messages.Invalid_Request");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['message' => 'Something is wrong', 'success' => false, 'error_msg' => $e->getMessage()], 500);
        }
    }



    public function getChildCategories(Request $request)
    {
        try {
            $subCategoryId = $request->sub_category_id ?? "";
            $childCategories = Category::where('parent_id', $subCategoryId)->where('is_active', 1)->where('is_deleted', 0)->get();

            return response()->json(['childCategories' => $childCategories, 'success' => true, 'message' => 'Data fetched'], 200);
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['message' => 'Something is wrong', 'success' => false, 'error_msg' => $e->getMessage()], 500);
        }
    }

    // app/Http/Controllers/Admin/ProductController.php

    public function updateStock(Request $request)
    {
        $productId = $request->input('productId');
        $inStock = $request->input('inStock');
        // Update the product in the database
        Product::where('id', $productId)->update(['in_stock' => $inStock]);

        return response()->json(['success' => true]);
    }

    // public function updateFeatured(Request $request)
    // {
    //     $productId = $request->input('productId');
    //     $isFeatured = $request->input('isFeatured');
    //     // Update the product in the database
    //     Product::where('id', $productId)->update(['is_featured' => $isFeatured]);

    //     return response()->json(['success' => true]);
    // }

    

    public function updateNewArrivals(Request $request)
    {
        $productId = $request->input('productId');
        $isFeatured = $request->input('isNewArrivals');
        // Update the product in the database
        Product::where('id', $productId)->update(['is_new_arrivals' => $isFeatured]);

        return response()->json(['success' => true]);
    }

    public function edit($token, Request $request)
    {

        $productId = decrypt($token);
        //session()->forget('product_images');
        //session()->forget('product_videos');
        if ($request->isMethod('post')) {

            $post_data = $request->all();

            #echo "<pre>";print_r($post_data);exit;
            if (session()->has('product_images')) {
                $post_data['images'] = session()->get('product_images');
            }

            if (session()->has('attributes')) {
                $post_data['attributes'] = session()->get('attributes');
            }
            if (session()->has('varient_product_image')) {
                $post_data['varient_product_image'] = session()->get('varient_product_image');
            }

            $is_active = 2;
            if (!empty($post_data['save']))
                $is_active = 1;

            $product = Product::find($productId);
            DB::beginTransaction();
            if (session()->has('product_images')) {
                $images = session()->get('product_images');
                $product->addProductImages($images, $productId);
                session()->forget('product_images');
            }
            if (session()->has('attributes')) {
                $attributes = session()->get('attributes');
                $product->addProductAttribute($attributes, $productId);
                session()->forget('attributes');
            }
            $rules = [
                'name' => 'required',
                'sku' => 'required',
                //'is_active' => 'required',
                'massage' => 'required',
                'category_id' => 'required',
                //'product_type' => 'required',
                //'categorys_id' => 'required',
                'buying_price' => 'required|numeric|gt:0',
                'selling_price' => 'required|numeric|gt:0',
                'specification' => 'required',
                'material' => 'required',
                'weight' => 'required',
                'weight_type' => 'required',
                'style' => 'required',
                'wash_care' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator);
            }

            $category_id = $request->category_id;

            if (isset($request->sub_category_id)) {

                $category_id = $request->sub_category_id;
            }

            if (isset($request->child_category_id)) {

                $category_id = $request->child_category_id;
            }
            $originalString = $request->name ?? "";
            $lowercaseString = Str::lower($originalString);
            $slug = Str::slug($lowercaseString, '-');

            $product->name = $request->name;
            $product->slug = $slug;
            $product->sku = $request->sku;
            $product->qty = $request->qty;
            $product->hsn = $request->hsn;
            $product->is_active = $is_active;
            $product->product_type = $request->product_type;
            $product->list_description = $request->massage;
            $product->related_product_categores_id = !empty($request->categorys_id) ? $request->categorys_id : 0;
            $product->related_product_subcategory_id = !empty($request->subcategory_id) ? $request->subcategory_id : 0;
            $product->related_products = (isset($request->Product_id) && is_array($request->Product_id)) ? implode(',', $request->Product_id) : '';
            $product->bar_code = $request->bar_code;
            $product->description = $request->description;
            $product->specification = $request->specification;
            $product->brand_id = $request->brand_id;
            $product->category_id = $category_id;
            $product->main_category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->child_category_id = $request->child_category_id;
            $product->buying_price = $request->buying_price;
            $product->discount = $request->discount;
            $product->discount_type = $request->discount_type;
            $product->selling_price = $request->selling_price;
            $product->is_including_taxes = $request->is_including_taxes;
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->meta_keywords = $request->meta_keywords;
            $product->seo_content = $request->seo_content;
            $product->material = $request->material;
            $product->weight = $request->weight;
            $product->weight_type = $request->weight_type;
            $product->style = $request->style;
            $product->country_origin = $request->country_origin;
            $product->wash_care = $request->wash_care;
            $product->collection_ids = isset($request->collection_ids) && is_array($request->collection_ids) ? implode(',', $request->collection_ids) : null;
            if (isset($request->product_tags) && !empty($request->product_tags)) {
                $product->product_tags = implode(',', $request->product_tags);
            } else {
                $product->product_tags = null; // or an empty string
            }



            if ($request->product_type == 2) {

                $product->product_varientname   = json_encode($request->varientname);
                $product->products_optionname  = json_encode($request->optionname);
            }
            $product->save();
            $lastInsertedId = $product->id;

            if ($request->product_type == 2) {
                if (isset($post_data['varient_product_image']) && !empty($post_data['varient_product_image'])) {
                    foreach ($post_data['varient_product_image'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'];
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            try {
                                $productColorImage->save();
                            } catch (\Exception $e) {
                                // Log or handle the exception
                                dd($e->getMessage());
                            }
                        }
                    }
                }
            }
            //added product attributes

            // $post_data = $request->all();



            if (!empty($request->attribute_ids)) {

                $attributeIds = $request->input('attribute_ids');
                $attributeValueIds = $request->input('attribute_value_ids');


                // echo count($attributeValueIds);die; 
                if (count($attributeIds) == count($attributeValueIds)) {
                    ProductAttribute::where('product_id', $lastInsertedId)->delete();
                    ProductAttributeValue::where('product_id', $lastInsertedId)->delete();
                    foreach ($attributeIds as $index => $attributeId) {

                        $attributeValueId = $attributeValueIds[$index];

                        if ($attributeValueId > 0) {
                            // Here we can create records in the database
                            $product_attributes = new ProductAttribute();
                            $product_attributes->product_id = $lastInsertedId;
                            $product_attributes->attribute_id = $attributeId;
                            $product_attributes->save();

                            $product_attributes_values = new ProductAttributeValue();
                            $product_attributes_values->product_id = $lastInsertedId;
                            $product_attributes_values->product_attribute_id = $product_attributes->id;
                            $product_attributes_values->attribute_value_id = $attributeValueId;
                            $product_attributes_values->save();
                        }
                    }
                }
            }
            //echo "<pre>"; print_r($post_data['images']);die;
            //end of product attributess

            //$variantProductImages = $request->file('variants_product_image');
            if ($request->product_type == 1) {

                ProductAttribute::where('product_id', $lastInsertedId)->delete();
                ProductVariant::where('product_id', $lastInsertedId)->delete();
                ProductAttributeValue::where('product_id', $lastInsertedId)->delete();
                ProductVariantCombination::where('product_id', $lastInsertedId)->delete();


                $variant = new ProductVariantCombination;
                $variant->product_id = $lastInsertedId;
                ProductVariant::where('product_id', $lastInsertedId)->delete();
                if (isset($post_data['images']) && !empty($post_data['images'])) {
                    //echo "<pre>"; print_r($post_data['images']);die;
                    foreach ($post_data['images'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'] ?? 0;
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            $productColorImage->save();
                        }
                    }
                }
                if ($request->variant_type == 'nosizenocolor') {
                    $variant->available_units  = $request->qty;
                    $productVariantNosize = new ProductVariant();
                    $productVariantNosize->variant_id = 3;
                    $productVariantNosize->product_id = $lastInsertedId;
                    $productVariantNosize->save();
                }
                if ($request->variant_type == 'size') {
                    $variant->size_variant_value_id  = $request->size_id;
                    $variant->available_units  = $request->qty;
                    $productVariantSize = new ProductVariant();
                    $productVariantSize->variant_id = 2;
                    $productVariantSize->product_id = $lastInsertedId;
                    $productVariantSize->save();
                }
                if ($request->variant_type == 'color') {
                    $variant->color_variant_value_id  = $request->color_id;
                    $variant->available_units  = $request->qty;
                    $productVariantColor = new ProductVariant();
                    $productVariantColor->variant_id = 1;
                    $productVariantColor->product_id = $lastInsertedId;
                    $productVariantColor->save();
                }
                $variant->selling_price = $request->selling_price ?? 0;
                $variant->available_units = 0;
                $variant->selling_units = $request->max_selling_units ?? 0;
                $variant->save();
            } else {
                ProductVariant::where('product_id', $lastInsertedId)->delete();
                ProductAttribute::where('product_id', $lastInsertedId)->delete();
                ProductAttributeValue::where('product_id', $lastInsertedId)->delete();
                ProductVariantCombination::where('product_id', $lastInsertedId)->delete();


                $productVariantColor = new ProductVariant();
                $productVariantColor->variant_id = 1;
                $productVariantColor->product_id = $lastInsertedId;
                $productVariantColor->save();



                $productVariantNosize = new ProductVariant();
                $productVariantNosize->variant_id = 3;
                $productVariantNosize->product_id = $lastInsertedId;
                $productVariantNosize->save();



                $productVariantSize = new ProductVariant();
                $productVariantSize->variant_id = 2;
                $productVariantSize->product_id = $lastInsertedId;
                $productVariantSize->save();


                if (isset($post_data['images']) && !empty($post_data['images'])) {
                    foreach ($post_data['images'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'];
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            $productColorImage->save();
                        }
                    }
                }
                if (isset($post_data['color_variants']) && is_array($post_data) && (is_array($post_data['color_variants']) || is_array($post_data['color_variants']))) {
                    ProductVariantCombination::where('product_id', $lastInsertedId)->delete();
                    if (isset($post_data['color_variants'])) {
                        foreach ($post_data['color_variants'] as $colorVariantId) {
                            if (isset($post_data['size_variants'])) {
                                foreach ($post_data['size_variants'] as $sizeVariantId) {
                                    $price =  $post_data['variant_selling_price'][$colorVariantId][$sizeVariantId] ?? 0; // MRP
                                    $availableUnits =  $post_data['variant_available_unit'][$colorVariantId][$sizeVariantId] ?? 0; // variant units
                                    $maxSellingUnits =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? 0; // variant selling price
                                    $variantSku =  $post_data['variant_sku'][$colorVariantId][$sizeVariantId] ?? ""; //var sku

                                    $variant = new ProductVariantCombination();
                                    $variant->product_id = $lastInsertedId;
                                    $variant->color_variant_value_id  = $colorVariantId;
                                    $variant->size_variant_value_id   = $sizeVariantId;
                                    $variant->sku = $variantSku;
                                    $variant->selling_price = $maxSellingUnits;
                                    $variant->available_units = $availableUnits;
                                    $variant->variant_mrp =  $price;
                                    $variant->save();
                                    $lastVariantId = $variant->id;
                                    if (!empty($productVariantSize)) {
                                        $productVariantValues = ProductVariantValue::where('product_veriant_id', $productVariantSize->id)->where('veriant_value_id',  $sizeVariantId)->first();
                                        if (empty($productVariantValues)) {
                                            $productVariantValue = new ProductVariantValue();
                                            $productVariantValue->product_veriant_id  = $productVariantSize->id;
                                            $productVariantValue->veriant_value_id = $sizeVariantId;
                                            $productVariantValue->save();
                                        }
                                    }
                                }
                            }

                            if (!empty($colorVariantId)) {

                                $productVariantValue = new ProductVariantValue();
                                $productVariantValue->product_veriant_id = $productVariantColor->id;
                                $productVariantValue->veriant_value_id = $colorVariantId;
                                $productVariantValue->save();
                            }
                        }
                    }
                }
            }

            if ($request->Main_images) {
                $ProductId = $product->id;
                $GELLERYIMAGES = $request->file('gallery_images');
                foreach ($request->file('Main_images') as $colorid => $file) {
                    if ($file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs("main/{$ProductId}", $fileName, 'public');
                        $productColorImage = new ProductColorImage();
                        $productColorImage->product_id = $ProductId;
                        $productColorImage->color_id = $colorid;
                        $productColorImage->image = $filePath;
                        $productColorImage->is_front = 1;
                        $productColorImage->save();
                    }
                    foreach ($GELLERYIMAGES[$colorid] as $newid => $files) {
                        if ($files) {
                            $fileName = time() . '_' . $files->getClientOriginalName();
                            $filePath = $files->storeAs("gallery/{$ProductId}", $fileName, 'public');
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $ProductId;
                            $productColorImage->color_id = $colorid;
                            $productColorImage->image = $filePath;
                            $productColorImage->is_front = 0;
                            $productColorImage->save();
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin-product-list')->with(['success' => "Product Updated Successfully."]);
        } else {

            $product = Product::where('id', $productId)->with('images', 'brand', 'category', 'subCategory', 'descriptions', 'productVariants')->first();


            $productvarient = array();
            $vaientvaluedata = array();
            // echo '<pre>';
            // print_r($product);die;
            if (!empty($product)) {

                if (isset($product->category->parentcategory->parent_id)) {

                    $parent_category_id = $product->category->parentcategory->parent_id;
                } elseif (isset($product->category->parent_id)) {

                    $parent_category_id = $product->category->parent_id;
                } else {

                    $parent_category_id = $product->category->id;
                }
                // get category when is active
                $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();

                $subCategories = $product->category_id
                    ? Category::where(['parent_id' => $product->category_id, 'is_active' => 1, 'is_deleted' => 0])->get()
                    : [];

                $brands = Brand::where('is_active', 1)->where('is_deleted', 0)->get();
                $variants = Variant::where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');
                $attributes = Attribute::where('is_active', 1)->where('is_deleted', 0)->get();
                $subcategory = Category::get();
                $subproducts = Product::get();
                //$colors = VariantValue::where('variant_id', 1)->get();
                $variants_color = Variant::where('name', 'color')->where('type', 2)->pluck('id');
                $colors = VariantValue::whereIn('variant_id', $variants_color)->get();
                $variants_size = Variant::where('name', 'size')->where('type', 2)->pluck('id');

                $sizes =  VariantValue::where('variant_id', $variants_size)->get();
                $attributesdatain = Variant::where('type', 2)->get();
                $producttags = Tag::where('is_active', 1)->where('is_deleted', 0)->get();

                $getColorDetail = ProductVariantCombination::where('product_id', $productId)->with('getColorDetail', 'ProductVariantCombination', 'ProductVariantCombination.getSize')->groupBy('color_variant_value_id')->get()->toArray();
                $variantData = [];

                foreach ($attributesdatain as $data) {
                    $variantData[$data->id] = VariantValue::where('variant_id', $data->id)->pluck('name', 'id');
                }
                $attributesdata = Variant::where('type', 2)->get();


                $image_data = $attribute_data = $variantValues = $variant_table['attribute'] = $variant_table['variant'] =  [];
                #echo "<pre>";print_r($product->images);die;
                if (isset($product->images) && !empty($product->images)) {
                    foreach ($product->images as $imagekey => $image) {
                        $images['name'] = explode('/', $image->getRawOriginal('image'));
                        $images['path'] = $image->getRawOriginal('image');
                        $images['is_front'] = $image->is_front;
                        $images['is_back'] = $image->is_back;
                        $images['ext'] = explode('.', $image->getRawOriginal('image'));
                        $image_data[] = $images;
                    }
                    session()->put('product_images', $image_data);

                    #echo "<pre>";print_r($image_data);exit;
                }

                // if (isset($product->productAttributes) && !empty($product->productAttributes)) {
                //     foreach ($product->productAttributes as $attribute) {

                //         $attr['id'] = $attribute->attribute->id;
                //         $attr['name'] = $attribute->attribute->name;
                //         foreach ($attribute->productAttributeValues as $k => $v) {
                //             $attrVal['id'] = $v->attributeValue->id;
                //             $attrVal['name'] = $v->attributeValue->name;
                //         }
                //         $attr['value'] = $attrVal;
                //         $attribute_data[] = $attr;
                //     }
                //     session()->put('attributes', $attribute_data);
                //     $variant_table['attribute'] = $attribute_data;
                // }

                // attributes data
                if (isset($product->productAttributes) && !empty($product->productAttributes)) {
                    $attribute_data = [];
                    foreach ($product->productAttributes as $attribute) {
                        if (isset($attribute->attribute) && $attribute->attribute->id != 0) {
                            $attr = [
                                'id' => $attribute->attribute->id,
                                'name' => $attribute->attribute->name,
                            ];

                            $attrVal = [];
                            foreach ($attribute->productAttributeValues as $v) {
                                if (isset($v->attributeValue) && $v->attributeValue->id != 0) {
                                    $attrVal[] = [
                                        'id' => $v->attributeValue->id,
                                        'name' => $v->attributeValue->name,
                                    ];
                                }
                            }
                            if (!empty($attrVal)) {
                                $attr['value'] = $attrVal;
                                $attribute_data[] = $attr;
                            }
                        }
                    }

                    if (!empty($attribute_data)) {
                        session()->put('attributes', $attribute_data);
                        $variant_table['attribute'] = $attribute_data;
                    }
                }

                //end attribute data

                if (isset($product->productVariants) && !empty($product->productVariants)) {
                    foreach ($product->productVariants as $pVariants) {
                        $variant = [];
                        if (isset($pVariants->variant)) {
                            //echo "<pre>"; print_r($pVariants->variantValues); die;
                            if (isset($pVariants->variantValues) && $pVariants->variantValues->isNotEmpty()) {
                                $variant['id'] = $pVariants->variant->id;
                                $variant['name'] = $pVariants->variant->name;
                                $innerVariant = [];
                                foreach ($pVariants->variantValues as $key => $value) {
                                    $innerVariant['id'] = ($value->variant_value ? $value->variant_value->id : '');
                                    $innerVariant['name'] = ($value->variant_value ? $value->variant_value->name : '');
                                    if (!strcasecmp($variant['name'], 'color')) {
                                        $innerVariant['code'] = ($value->variant_value ? $value->variant_value->color_code : '');
                                    } else {
                                        $innerVariant['code'] = '';
                                    }
                                    $innerVariant['price'] = $value['price'];
                                    $innerVariant['available'] = $value['available'];
                                    $variant['value'][] = $innerVariant;
                                }
                                $variantValues[] = $variant;
                            }
                        }
                    }

                    session()->put('variants', $variantValues);
                    $variant_table['variant'] = $variantValues;
                }


                $editimgs = ProductColorImage::where(['product_id' => $productId])->get();
                $colorimages = array();
                foreach ($editimgs as $singlecolorimg) {
                    // if(isset($colorimages[$singlecolorimg])){
                    $colorimages[$singlecolorimg->color_id]['images'][] = $singlecolorimg;
                    // }else{
                    //     $colorimages[$singlecolorimg->color_id]['images'][] =$singlecolorimg->image; 
                    // }
                }
                $simpleVeriantValue = [];
                $varintValueIds = [];
                /******************************Get Configurable Product Data*************************************/
                if ($product->product_type == 2) {
                    $vaientvaluedata = array();
                    $productdatain = product::where('parent_id', $product->id)->get();
                    $productvarient = array();


                    foreach ($productdatain as $keydata => $prodata) {
                        $singlevaluevarientdat = array();
                        $singlevaluevarientdat['selling_price'] = $prodata->selling_price;
                        $singlevaluevarientdat['name'] = $prodata->veriant_name;
                        $singlevaluevarientdat['qty'] = $prodata->qty;
                        $singlevaluevarientdat['subproduct_id'] = $prodata->id;
                        $vaientvaluedata[$prodata->veriant_name] = $singlevaluevarientdat;

                        $singleproductvarient = array();
                        $singleproductvarient['id'] = $prodata->id;
                        $singleproductvarient['buying_price'] = $prodata->buying_price;
                        $singleproductvarient['discount'] = $prodata->discount;
                        $singleproductvarient['discount_type'] = $prodata->discount_type;
                        $singleproductvarient['selling_price'] = $prodata->selling_price;
                        $singleproductvarient['in_stock'] = $prodata->in_stock;
                        $singleproductvarient['qty'] = $prodata->qty;
                        $attributevaluearray = array();
                        $productattribute = ProductAttribute::leftjoin('variants', 'variants.id', '=', 'product_attributes.attribute_id')->where('product_id', $prodata->id)->select('product_attributes.*', 'variants.name')->get();
                        foreach ($productattribute as $singleattribute) {
                            $singleattarr = array();

                            $singleattarr['attribute_id'] = $singleattribute->attribute_id;
                            $singleattarr['attribute_name'] = $singleattribute->name;

                            $productattribute = ProductAttributeValue::leftjoin('variant_values', 'variant_values.id', '=', 'product_attribute_values.attribute_value_id')->where('product_attribute_id', $singleattribute->id)->select('product_attribute_values.*', 'variant_values.name')->first();
                            $singleattarr['value_id'] = $productattribute->attribute_value_id;
                            $singleattarr['value_name'] = $productattribute->name;
                            $attributevaluearray[] = $singleattarr;
                        }
                        $singleproductvarient['attributedata'] = $attributevaluearray;

                        $productvarient[] = $singleproductvarient;
                    }
                    /******************************Get Configurable Product Data*************************************/
                } elseif ($product->product_type == 1) {
                    $simpleVeriantValue = SimpleVeriantValue::where('product_id', $product->id)->first();
                    $varintValue = [];
                    if (!empty($simpleVeriantValue)) {
                        $variant_values = explode(',', $simpleVeriantValue->variant_values);
                        $varintValueIds = Variant::whereIn('id', $variant_values)->pluck('id')->toArray();
                    }
                    $simpleProductVariantCombination = ProductVariantCombination::where('product_id', $product->id)->first();
                    //echo "<pre>";print_r($simpleProductVariantCombination);die;
                }
                $type = 'edit';
                $collections = ProductCollection::pluck('title', 'id')->toArray();
                $simpleVariants = Variant::where('is_active', 1)->where('is_deleted', 0)->where('type', 1)->pluck('name', 'id');
                return view('admin.product_new.create', compact('categories', 'vaientvaluedata', 'productvarient', 'attributesdata', 'variantData', 'subproducts', 'subcategory', 'brands', 'variants', 'productId', 'type', 'product', 'subCategories', 'image_data', 'variant_table', 'attributes', 'colorimages', 'parent_category_id', 'colors', 'sizes', 'getColorDetail', 'producttags', 'collections', 'simpleVariants', 'simpleVeriantValue', 'varintValueIds'));
            } else {
                return redirect()->back()->with(['error' => 'Invalid Request']);
            }
        }
    }

    public function retviewData($newProductid)
    {

        $productdatain = product::where('parent_id', $newProductid)->get();
        $productvarient = array();
        $vaientvaluedata = array();
        foreach ($productdatain as $keydata => $prodata) {
            $singlevaluevarientdat = array();
            $singlevaluevarientdat['selling_price'] = $prodata->selling_price;
            $singlevaluevarientdat['name'] = $prodata->veriant_name;
            $singlevaluevarientdat['qty'] = $prodata->qty;
            $singlevaluevarientdat['subproduct_id'] = $prodata->id;
            $vaientvaluedata[$prodata->veriant_name] = $singlevaluevarientdat;

            $singleproductvarient = array();
            $singleproductvarient['id'] = $prodata->id;
            $singleproductvarient['buying_price'] = $prodata->buying_price;
            $singleproductvarient['discount'] = $prodata->discount;
            $singleproductvarient['discount_type'] = $prodata->discount_type;
            $singleproductvarient['selling_price'] = $prodata->selling_price;
            $singleproductvarient['in_stock'] = $prodata->in_stock;
            $singleproductvarient['qty'] = $prodata->qty;
            $attributevaluearray = array();
            $productattribute = ProductAttribute::leftjoin('variants', 'variants.id', '=', 'product_attributes.attribute_id')->where('product_id', $prodata->id)->select('product_attributes.*', 'variants.name')->get();
            foreach ($productattribute as $singleattribute) {
                $singleattarr = array();

                $singleattarr['attribute_id'] = $singleattribute->attribute_id;
                $singleattarr['attribute_name'] = $singleattribute->name;

                $productattribute = ProductAttributeValue::leftjoin('variant_values', 'variant_values.id', '=', 'product_attribute_values.attribute_value_id')->where('product_attribute_id', $singleattribute->id)->select('product_attribute_values.*', 'variant_values.name')->first();
                $singleattarr['value_id'] = $productattribute->attribute_value_id;
                $singleattarr['value_name'] = $productattribute->name;
                $attributevaluearray[] = $singleattarr;
            }
            $singleproductvarient['attributedata'] = $attributevaluearray;

            $productvarient[] = $singleproductvarient;
        }
        return $vaientvaluedata;
    }

    public function create_newOld(Request $request)
    {
        if ($request->isMethod('post')) {

            $post_data = $request->all();
            if (session()->has('product_images')) {
                $post_data['images'] = session()->get('product_images');
            }
            if (session()->has('attributes')) {
                $post_data['attributes'] = session()->get('attributes');
            }
            if (session()->has('varient_product_image')) {
                $post_data['varient_product_image'] = session()->get('varient_product_image');
            }
            $rules = [
                'name' => 'required|unique:products,name',
                'sku' => 'required',
                'massage' => 'required',
                'category_id' => 'required',
                //'product_type' => 'required',
                //'categorys_id' => 'required',
                'selling_price' => 'required|numeric|gt:0',
                'buying_price' => 'required|numeric|gt:0',
                'specification' => 'required',
                'material' => 'required',
                'style' => 'required',
                'wash_care' => 'required',
                'is_including_taxes' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator);
            }

            session()->forget('varient_product_image');
            session()->forget('product_images');
            DB::beginTransaction();
            $is_active = 2;
            if (!empty($post_data['save']))
                $is_active = 1;
            $originalString = $request->name ?? "";
            $lowercaseString = Str::lower($originalString);
            $slug = Str::slug($lowercaseString, '-');
            $category_id = $request->category_id;
            if (isset($request->sub_category_id)) {
                $category_id = $request->sub_category_id;
            }
            if (isset($request->child_category_id)) {
                $category_id = $request->child_category_id;
            }
            // $productArray = [
            //     'name' => $request->name,
            //     'slug' => $slug,
            //     'product_number' => '00',
            //     'sku' =>  $request->sku,
            //     'qty' => $request->qty,
            //     'hsn' => $request->hsn,
            //     'is_active' => $is_active,
            //     'product_type' => $request->name,
            //     'list_description' => $request->name,
            //     'related_product_categores_id' => $request->name,
            //     'related_product_subcategory_id' => $request->name,
            //     'list_description' => $request->name,
            //     'related_products' => $request->name,
            //     'bar_code' => $request->name,
            //     'description' => $request->name,
            //     'specification' => $request->name,
            //     'brand_id' => $request->name,
            //     'category_id' => $request->name,
            //     'main_category_id' => $request->name,
            //     'sub_category_id' => $request->name,
            //     'child_category_id' => $request->name,
            //     'buying_price' => $request->name,
            //     'discount' => $request->name,
            //     'discount_type' => $request->name,
            //     'selling_price' => $request->name,
            //     'max_selling_units' => $request->name,
            //     'min_selling_units' => $request->name,
            //     'is_including_taxes' => $request->name,
            //     'meta_title' => $request->name,
            //     'meta_description' => $request->name,
            //     'meta_keywords' => $request->name,
            //     'seo_content' => $request->name,
            //     'material' => $request->name,
            //     'weight' => $request->name,
            //     'style' => $request->name,
            //     'country_origin' => $request->name,
            //     'wash_care' => $request->name,

            // ];

            $product = new Product();
            $product->name = $request->name;
            $product->slug = $slug;
            $product->product_number = '00';
            $product->sku = $request->sku;
            $product->qty = $request->qty;
            $product->hsn = $request->hsn;
            $product->is_active = $is_active;
            $product->product_type = 1;
            $product->list_description = $request->massage;
            $product->related_product_categores_id = $request->categorys_id;
            $product->related_product_subcategory_id = $request->subcategory_id;

            if (isset($request->Product_id) && !empty($request->Product_id)) {
                $product->related_products = implode(',', $request->Product_id);
            } else {
                $product->related_products = null; // or an empty string
            }
            $product->bar_code = $request->bar_code;
            $product->description = $request->description;
            $product->specification = $request->specification;
            $product->brand_id = $request->brand_id;
            $product->category_id =  $category_id;
            $product->main_category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->child_category_id = $request->child_category_id;
            $product->buying_price = $request->buying_price;
            $product->discount = $request->discount;
            $product->discount_type = $request->discount_type;
            $product->selling_price = $request->selling_price;
            $product->max_selling_units = $request->max_selling_units;
            $product->min_selling_units = $request->min_selling_units;
            $product->is_including_taxes = $request->is_including_taxes;
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->meta_keywords = $request->meta_keywords;
            $product->seo_content = $request->seo_content;
            $product->material = $request->material;
            $product->weight = $request->weight;
            $product->style = $request->style;
            $product->country_origin = $request->country_origin;
            $product->wash_care = $request->wash_care;

            // if ($request->product_type == 2) {
            //     $product->product_varientname   = json_encode($request->productvarientname);
            //     $product->products_optionname  = json_encode($request->optionname);
            // }
            $product->save();

            $lastInsertedId = $product->id;

            //saving variant color Images 
            if ($request->product_type == 2) {
                if (isset($post_data['varient_product_image']) && !empty($post_data['varient_product_image'])) {
                    foreach ($post_data['varient_product_image'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'];
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            try {
                                $productColorImage->save();
                            } catch (\Exception $e) {
                                // Log or handle the exception
                                dd($e->getMessage());
                            }
                        }
                    }
                }
            }

            //added product attributes
            $post_data = $request->all();
            if (!empty($request->attribute_ids)) {

                $attributeIds = $request->input('attribute_ids');
                $attributeValueIds = $request->input('attribute_value_ids');
                if (count($attributeIds) == count($attributeValueIds)) {
                    foreach ($attributeIds as $index => $attributeId) {
                        $attributeValueId = $attributeValueIds[$index];

                        // Here we can create records in the database
                        $product_attributes = new ProductAttribute();
                        $product_attributes->product_id = $lastInsertedId;
                        $product_attributes->attribute_id = $attributeId;
                        $product_attributes->save();

                        $product_attributes_values = new ProductAttributeValue();
                        $product_attributes_values->product_id = $lastInsertedId;
                        $product_attributes_values->product_attribute_id = $product_attributes->id;
                        $product_attributes_values->attribute_value_id = $attributeValueId;
                        $product_attributes_values->save();
                    }
                }
            }
            //end of product attributess

            //$variantProductImages = $request->file('variants_product_image');
            if ($request->product_type == 1) {
                $variant = new ProductVariantCombination;
                $variant->product_id = $lastInsertedId;
                if ($request->variant_type == 'nosizenocolor') {
                    $variant->size_variant_value_id  = $request->variant_value;
                    $productVariantNosize = new ProductVariant();
                    $productVariantNosize->variant_id = 3;
                    $productVariantNosize->product_id = $lastInsertedId;
                    $productVariantNosize->save();
                }
                if ($request->variant_type == 'size') {
                    $variant->size_variant_value_id  = $request->variant_value;
                    $productVariantSize = new ProductVariant();
                    $productVariantSize->variant_id = 2;
                    $productVariantSize->product_id = $lastInsertedId;
                    $productVariantSize->save();
                }
                if ($request->variant_type == 'color') {
                    $variant->color_variant_value_id  = $request->variant_value;
                    $productVariantColor = new ProductVariant();
                    $productVariantColor->variant_id = 1;
                    $productVariantColor->product_id = $lastInsertedId;
                    $productVariantColor->save();
                }
                $variant->selling_price = $request->selling_price ?? 0;
                $variant->available_units = 0;
                $variant->selling_units = $request->max_selling_units ?? 0;
                $variant->save();
            } else {
                if (isset($post_data['images']) && !empty($post_data['images'])) {
                    foreach ($post_data['images'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'];
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            $productColorImage->save();
                        }
                    }
                }
                if (is_array($post_data) && (is_array($post_data['color_variants']) || is_array($post_data['color_variants']))) {
                    if ($request->variant_type == 'colour') {
                        $productVariantColor = new ProductVariant();
                        $productVariantColor->variant_id = 1;
                        $productVariantColor->product_id = $lastInsertedId;
                        $productVariantColor->save();
                    }

                    if ($request->variant_type == 'nosizenocolor') {
                        $productVariantNosize = new ProductVariant();
                        $productVariantNosize->variant_id = 3;
                        $productVariantNosize->product_id = $lastInsertedId;
                        $productVariantNosize->save();
                    }

                    if ($request->variant_type == 'size') {
                        $productVariantSize = new ProductVariant();
                        $productVariantSize->variant_id = 2;
                        $productVariantSize->product_id = $lastInsertedId;
                        $productVariantSize->save();
                    }



                    foreach ($post_data['color_variants'] as $colorVariantId) {
                        if (count($post_data['size_variants']) > 0) {
                            foreach ($post_data['size_variants'] as $sizeVariantId) {
                                $price =  $post_data['variant_selling_price'][$colorVariantId][$sizeVariantId] ?? 0;
                                $availableUnits =  $post_data['variant_available_unit'][$colorVariantId][$sizeVariantId] ?? 0;
                                $maxSellingUnits =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? 0;
                                $variantSku =  $post_data['variant_sku'][$colorVariantId][$sizeVariantId] ?? "";
                                $variantSellingPrice =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? "";

                                $variant = new ProductVariantCombination();
                                $variant->product_id = $lastInsertedId;
                                $variant->color_variant_value_id  = $colorVariantId;
                                $variant->size_variant_value_id   = $sizeVariantId;
                                $variant->sku = $variantSku;
                                $variant->buying_price =  $price;
                                $variant->available_units = $availableUnits;
                                $variant->selling_units =  $maxSellingUnits;
                                $variant->selling_price =  $variantSellingPrice;
                                $variant->save();
                                $lastVariantId = $variant->id;

                                if (!empty($productVariantSize)) {
                                    $productVariantValues = ProductVariantValue::where('product_veriant_id', $productVariantSize->id)->where('veriant_value_id',  $sizeVariantId)->first();
                                    if (empty($productVariantValues)) {
                                        $productVariantValue = new ProductVariantValue();
                                        $productVariantValue->product_veriant_id  = $productVariantSize->id;
                                        $productVariantValue->veriant_value_id = $sizeVariantId;
                                        $productVariantValue->save();
                                    }
                                }
                            }
                        }
                        if (!empty($colorVariantId) && !empty($productVariantColor)) {

                            $productVariantValue = new ProductVariantValue();
                            $productVariantValue->product_veriant_id = $productVariantColor->id;
                            $productVariantValue->veriant_value_id = $colorVariantId;
                            $productVariantValue->save();
                        }
                    }
                }
            }

            if ($request->Main_images) {
                $ProductId = $product->id;
                $GELLERYIMAGES = $request->file('gallery_images');
                foreach ($request->file('Main_images') as $colorid => $file) {
                    if ($file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs("main/{$ProductId}", $fileName, 'public');
                        $productColorImage = new ProductColorImage();
                        $productColorImage->product_id = $ProductId;
                        $productColorImage->color_id = $colorid;
                        $productColorImage->image = $filePath;
                        $productColorImage->is_front = 1;
                        $productColorImage->save();
                    }
                    foreach ($GELLERYIMAGES[$colorid] as $newid => $files) {
                        if ($files) {
                            $fileName = time() . '_' . $files->getClientOriginalName();
                            $filePath = $files->storeAs("gallery/{$ProductId}", $fileName, 'public');
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $ProductId;
                            $productColorImage->color_id = $colorid;
                            $productColorImage->image = $filePath;
                            $productColorImage->is_front = 0;
                            $productColorImage->save();
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin-product-list')->with(['success' => "Product Added Successfully."]);
        } else {
            //print_r(session()->get('product_images')); die;
            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();
            $brands = Brand::where('is_active', 1)->where('is_deleted', 0)->get();
            $producttags = Tag::where('is_active', 1)->where('is_deleted', 0)->get();
            $variants = Variant::where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');
            $attributes = Attribute::where('is_active', 1)->where('is_deleted', 0)->get();
            $colors = VariantValue::where('variant_id', 1)->get();
            $sizes =  VariantValue::where('variant_id', 2)->orderBy('id', 'ASC')->get();


            $attributesdatain = Variant::where('type', 2)->get();
            $variantData = [];
            foreach ($attributesdatain as $data) {
                $variantData[$data->id] = VariantValue::where('variant_id', $data->id)->pluck('name', 'id');
            }

            $subcategory = Category::get();
            $subproducts = Product::get();
            $attributesdata = Variant::where('type', 2)->get();

            if ($request->session()->has('currentProductId') || session()->has('variants') || session()->has('attributes') || session()->has('product_images')) {
                $request->session()->forget('currentProductId');
                session()->forget('variants');
                session()->forget('attributes');
                session()->forget('product_images');
            }
            $type = 'create';
            $variant_values = [];
            return view('admin.product_new.create', compact('categories', 'attributesdata', 'variantData', 'subcategory', 'subproducts', 'brands', 'producttags', 'type', 'variants', 'variant_values', 'attributes', 'colors', 'sizes'));
        }
    }

    public function create_new(Request $request)
    {   //session()->forget('varient_product_image');
        //session()->forget('product_images');
        if ($request->isMethod('post')) {

            $post_data = $request->all();
            $post_data['images'] = session()->get('product_images');
            $post_data['videos'] = session()->get('product_videos');
            // echo "create_new<pre>";
            // print_r($post_data);
            // echo "<pre>";
            // print_r($post_data['videos']);
            // echo "<pre>";
            // print_r($post_data['images']);
            // die;
            if (session()->has('product_images')) {
                $post_data['images'] = session()->get('product_images');
            }
            if (session()->has('product_videos')) {
                $post_data['videos'] = session()->get('product_videos');
            }
            if (session()->has('attributes')) {
                $post_data['attributes'] = session()->get('attributes');
            }
            if (session()->has('varient_product_image')) {
                $post_data['varient_product_image'] = session()->get('varient_product_image');
            }
            $rules = [
                'name' => 'required|unique:products,name',
                'sku' => 'required',
                'massage' => 'required',
                'category_id' => 'required',
                //'product_type' => 'required',
                //'categorys_id' => 'required',
                'selling_price' => 'required|numeric|gt:0',
                'buying_price' => 'required|numeric|gt:0',
                'specification' => 'required',
                'material' => 'required',
                'weight' => 'required',
                'weight_type' => 'required',
                'style' => 'required',
                'wash_care' => 'required',
                'is_including_taxes' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }

            session()->forget('varient_product_image');
            session()->forget('product_images');
            DB::beginTransaction();
            $is_active = 2;
            if (!empty($post_data['save']))
                $is_active = 1;
            $originalString = $request->name ?? "";
            $lowercaseString = Str::lower($originalString);
            $slug = Str::slug($lowercaseString, '-');
            $category_id = $request->category_id;
            if (isset($request->sub_category_id)) {
                $category_id = $request->sub_category_id;
            }
            if (isset($request->child_category_id)) {
                $category_id = $request->child_category_id;
            }
            $relatedProducts = isset($request->Product_id) && is_array($request->Product_id) ? implode(',', $request->Product_id) : null;
            $collectionIds = isset($request->collection_ids) && is_array($request->collection_ids) ? implode(',', $request->collection_ids) : null;;
            $productTags =  isset($request->product_tags) && is_array($request->product_tags) ? implode(',', $request->product_tags) : null;

            $productArray = [
                'name' => $request->name,
                'slug' => $slug,
                'product_number' => '00',
                'sku' =>  $request->sku,
                'qty' => $request->qty,
                'hsn' => $request->hsn,
                'is_active' => $is_active,
                'product_type' => $request->product_type,
                'list_description' => $request->massage,
                'related_product_categores_id' => $request->categorys_id,
                'related_product_subcategory_id' => $request->subcategory_id,
                'related_products' => $relatedProducts,
                'bar_code' => $request->bar_code,
                'description' => $request->description,
                'specification' => $request->specification,
                'brand_id' => $request->brand_id,
                'category_id' => $category_id,
                'main_category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'buying_price' => $request->buying_price,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'selling_price' => $request->selling_price,
                'max_selling_units' => $request->max_selling_units,
                'min_selling_units' => $request->min_selling_units,
                'is_including_taxes' => $request->is_including_taxes,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'seo_content' => $request->seo_content,
                'material' => $request->material,
                'weight' => $request->weight,
                'weight_type' => $request->weight_type,
                'style' => $request->style,
                'country_origin' => $request->country_origin,
                'wash_care' => $request->wash_care,
                'product_tags' => $productTags,
                'collection_ids' => $collectionIds,
            ];
            //get last insertted id
            $product = Product::create($productArray);
            $lastInsertedId = $product->id;

            //saving variant color Images  when product type if configurable
            if ($request->product_type == 2) {
                if (isset($post_data['varient_product_image']) && !empty($post_data['varient_product_image'])) {
                    foreach ($post_data['varient_product_image'] as $colorid => $file) {
                        if ($file) {
                            $productImageArray = [
                                'product_id' => $lastInsertedId,
                                'color_id'  => $file['color_id'],
                                'image'  => $file['path'],
                                'is_front'  => $file['is_front'],
                                'is_back'  => $file['is_back'],
                            ];
                            ProductColorImage::create($productImageArray);
                        }
                    }
                }
            }

            //added product attributes
            $post_data = $request->all();
            if (!empty($request->attribute_ids)) {

                $attributeIds = $request->input('attribute_ids');
                $attributeValueIds = $request->input('attribute_value_ids');
                if (count($attributeIds) == count($attributeValueIds)) {
                    foreach ($attributeIds as $index => $attributeId) {
                        $attributeValueId = $attributeValueIds[$index];

                        // Here we can create records in the database
                        $product_attributes = new ProductAttribute();
                        $product_attributes->product_id = $lastInsertedId;
                        $product_attributes->attribute_id = $attributeId;
                        $product_attributes->save();

                        $product_attributes_values = new ProductAttributeValue();
                        $product_attributes_values->product_id = $lastInsertedId;
                        $product_attributes_values->product_attribute_id = $product_attributes->id;
                        $product_attributes_values->attribute_value_id = $attributeValueId;
                        $product_attributes_values->save();
                    }
                }
            }
            //end of product attributess

            //$variantProductImages = $request->file('variants_product_image');
            if ($request->product_type == 1) {


                if (isset($post_data['images']) && !empty($post_data['images'])) {
                    foreach ($post_data['images'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'] ?? 0;
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            $productColorImage->save();
                        }
                    }
                }

                if (isset($post_data['videos']) && !empty($post_data['videos'])) {
                    foreach ($post_data['videos'] as $colorid => $file) {
                        if ($file) {
                            $ProductColorVideo = new ProductColorVideo();
                            $ProductColorVideo->product_id = $lastInsertedId;
                            $ProductColorVideo->color_id = $file['color_id'] ?? 0;
                            $ProductColorVideo->video = $file['path'];
                            $ProductColorVideo->is_front = $file['is_front'];
                            $ProductColorVideo->is_back = $file['is_back'];
                            $ProductColorVideo->save();
                        }
                    }
                }

                $totalSimpleUnits = 0;
                if (!empty($request->simplevariant)) {

                    $productVariant = ProductVariant::create([
                        'variant_id' => $request->simplevariant,
                        'product_id' => $lastInsertedId,
                    ]);
                    if (!empty($request->variant_valueas)) {
                        foreach ($request->variant_valueas as $variant_valueas_key => $variant_valueas) {
                            $simpleVarintMrp = $request->simple_mrp[$variant_valueas] ?? 0;
                            $simpleVarintSellPrice = $request->simple_units_selling_price[$variant_valueas] ?? 0;
                            $simpleVarintUnits = $request->simple_units[$variant_valueas] ?? 0;
                            $simple_varint_sku = $request->simple_varint_sku[$variant_valueas] ?? 0;

                            $variant = new ProductVariantCombination();
                            $variant->product_id = $lastInsertedId;
                            $variant->simple_varint_value   = $variant_valueas;
                            $variant->sku = $simple_varint_sku;
                            $variant->available_units = $simpleVarintUnits;
                            $variant->selling_price   =  $simpleVarintSellPrice;
                            $variant->variant_mrp   =  $simpleVarintMrp;
                            $variant->save();
                            $lastVariantId = $productVariant->id;
                            $totalSimpleUnits += $simpleVarintUnits;
                        }
                    }
                }
                SimpleVeriantValue::create(['product_id' => $lastInsertedId, 'variant_id' => $request->simplevariant, 'total_units' => $totalSimpleUnits, 'variant_values' => implode(',', $request->variant_valueas)]);
                // $variant = new ProductVariantCombination; 
                // $variant->product_id = $lastInsertedId;
                // if ($request->variant_type == 'nosizenocolor') {
                //     $variant->size_variant_value_id  = $request->variant_value;
                //     $productVariantNosize = new ProductVariant();
                //     $productVariantNosize->variant_id = 3;
                //     $productVariantNosize->product_id = $lastInsertedId;
                //     $productVariantNosize->save();
                // }
                // if ($request->variant_type == 'size') {
                //     $variant->size_variant_value_id  = $request->variant_value;
                //     $productVariantSize = new ProductVariant();
                //     $productVariantSize->variant_id = 2;
                //     $productVariantSize->product_id = $lastInsertedId;
                //     $productVariantSize->save();
                // }
                // if ($request->variant_type == 'color') {
                //     $variant->color_variant_value_id  = $request->variant_value;
                //     $productVariantColor = new ProductVariant();
                //     $productVariantColor->variant_id = 1;
                //     $productVariantColor->product_id = $lastInsertedId;
                //     $productVariantColor->save();
                // }
                // $variant->selling_price = $request->selling_price ?? 0;
                // $variant->available_units = 0;
                // $variant->selling_units = $request->max_selling_units ?? 0;
                // $variant->save();
            } else {
                if (isset($post_data['images']) && !empty($post_data['images'])) {
                    foreach ($post_data['images'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'];
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            $productColorImage->save();
                        }
                    }
                }
                if (isset($post_data['color_variants']) && is_array($post_data) && (is_array($post_data['color_variants']) || is_array($post_data['color_variants']))) {
                    if ($request->variant_type == 'colour') {
                        $productVariantColor = new ProductVariant();
                        $productVariantColor->variant_id = 1;
                        $productVariantColor->product_id = $lastInsertedId;
                        $productVariantColor->save();
                    }

                    if ($request->variant_type == 'nosizenocolor') {
                        $productVariantNosize = new ProductVariant();
                        $productVariantNosize->variant_id = 3;
                        $productVariantNosize->product_id = $lastInsertedId;
                        $productVariantNosize->save();
                    }

                    if ($request->variant_type == 'size') {
                        $productVariantSize = new ProductVariant();
                        $productVariantSize->variant_id = 2;
                        $productVariantSize->product_id = $lastInsertedId;
                        $productVariantSize->save();
                    }


                    foreach ($post_data['color_variants'] as $colorVariantId) {
                        if (isset($post_data['size_variants']) && count($post_data['size_variants']) > 0) {
                            foreach ($post_data['size_variants'] as $sizeVariantId) {
                                $price =  $post_data['variant_selling_price'][$colorVariantId][$sizeVariantId] ?? 0;
                                $availableUnits =  $post_data['variant_available_unit'][$colorVariantId][$sizeVariantId] ?? 0;
                                //$maxSellingUnits =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? 0;
                                $variantSku =  $post_data['variant_sku'][$colorVariantId][$sizeVariantId] ?? "";
                                $variantSellingPrice =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? "";

                                $variant = new ProductVariantCombination();
                                $variant->product_id = $lastInsertedId;
                                $variant->color_variant_value_id  = $colorVariantId;
                                $variant->size_variant_value_id   = $sizeVariantId;
                                $variant->sku = $variantSku;
                                $variant->available_units = $availableUnits;
                                $variant->selling_price   =  $variantSellingPrice;
                                $variant->variant_mrp   =  $price;
                                $variant->save();
                                $lastVariantId = $variant->id;

                                if (!empty($productVariantSize)) {
                                    $productVariantValues = ProductVariantValue::where('product_veriant_id', $productVariantSize->id)->where('veriant_value_id',  $sizeVariantId)->first();
                                    if (empty($productVariantValues)) {
                                        $productVariantValue = new ProductVariantValue();
                                        $productVariantValue->product_veriant_id  = $productVariantSize->id;
                                        $productVariantValue->veriant_value_id = $sizeVariantId;
                                        $productVariantValue->save();
                                    }
                                }
                            }
                        }
                        if (!empty($colorVariantId) && !empty($productVariantColor)) {

                            $productVariantValue = new ProductVariantValue();
                            $productVariantValue->product_veriant_id = $productVariantColor->id;
                            $productVariantValue->veriant_value_id = $colorVariantId;
                            $productVariantValue->save();
                        }
                    }
                }
            }

            if ($request->Main_images) {
                $ProductId = $product->id;
                $GELLERYIMAGES = $request->file('gallery_images');
                foreach ($request->file('Main_images') as $colorid => $file) {
                    if ($file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs("main/{$ProductId}", $fileName, 'public');
                        $productColorImage = new ProductColorImage();
                        $productColorImage->product_id = $ProductId;
                        $productColorImage->color_id = $colorid;
                        $productColorImage->image = $filePath;
                        $productColorImage->is_front = 1;
                        $productColorImage->save();
                    }
                    foreach ($GELLERYIMAGES[$colorid] as $newid => $files) {
                        if ($files) {
                            $fileName = time() . '_' . $files->getClientOriginalName();
                            $filePath = $files->storeAs("gallery/{$ProductId}", $fileName, 'public');
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $ProductId;
                            $productColorImage->color_id = $colorid;
                            $productColorImage->image = $filePath;
                            $productColorImage->is_front = 0;
                            $productColorImage->save();
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin-product-list')->with(['success' => "Product Added Successfully."]);
        } else {
            //print_r(session()->get('product_images')); die;
            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();
            $brands = Brand::where('is_active', 1)->where('is_deleted', 0)->get();
            $producttags = Tag::where('is_active', 1)->where('is_deleted', 0)->get();
            $variants = Variant::where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');
            $attributes = Attribute::where('is_active', 1)->where('is_deleted', 0)->get();

            // $colors = VariantValue::where('variant_id', 1)->get();
            // $sizes =  VariantValue::where('variant_id', 2)->orderBy('id', 'ASC')->get();

            // start color and size field get

            $variants_color = Variant::where('name', 'color')->pluck('id');
            $variants_size = Variant::where('name', 'size')->pluck('id');

            $colors = VariantValue::whereIn('variant_id', $variants_color)->get();
            $sizes = VariantValue::whereIn('variant_id', $variants_size)->orderBy('id', 'ASC')->get();

            // End color and size field get


            $attributesdatain = Variant::where('type', 2)->get();
            $variantData = [];
            foreach ($attributesdatain as $data) {
                $variantData[$data->id] = VariantValue::where('variant_id', $data->id)->pluck('name', 'id');
            }

            $subcategory = Category::get();
            $subproducts = Product::get();
            $attributesdata = Variant::where('type', 2)->get();

            if ($request->session()->has('currentProductId') || session()->has('variants') || session()->has('attributes') || session()->has('product_images')) {
                $request->session()->forget('currentProductId');
                session()->forget('variants');
                session()->forget('attributes');
                session()->forget('product_images');
            }
            $type = 'create';
            $variant_values = [];
            $collections = ProductCollection::pluck('title', 'id')->toArray();
            $simpleVariants = Variant::where('is_active', 1)->where('is_deleted', 0)->where('type', 1)->pluck('name', 'id');
            return view('admin.product_new.create', compact('categories', 'attributesdata', 'variantData', 'subcategory', 'subproducts', 'brands', 'producttags', 'type', 'variants', 'variant_values', 'attributes', 'colors', 'sizes', 'collections', 'simpleVariants'));
        }
    }

    public function create_new_demo(Request $request)
    {   //session()->forget('varient_product_image');
        //session()->forget('product_images');
        if ($request->isMethod('post')) {

            //$post_data = $request->all();
            //echo "hello_create_new_demo<pre>";print_r($request->all());exit;



            $post_data = $request->all();
            $post_data['images'] = session()->get('product_images');
            $post_data['videos'] = session()->get('product_videos');
            #echo "<pre>";print_r($post_data);
            #echo "<pre>";print_r($post_data['videos']);
            #echo "<pre>";print_r($post_data['images']);die;
            if (session()->has('product_images')) {
                $post_data['images'] = session()->get('product_images');
            }
            if (session()->has('product_videos')) {
                $post_data['videos'] = session()->get('product_videos');
            }
            if (session()->has('attributes')) {
                $post_data['attributes'] = session()->get('attributes');
            }
            if (session()->has('varient_product_image')) {
                $post_data['varient_product_image'] = session()->get('varient_product_image');
            }
            $rules = [
                'name' => 'required|unique:products,name',
                'sku' => 'required',
                'massage' => 'required',
                'category_id' => 'required',
                //'product_type' => 'required',
                //'categorys_id' => 'required',
                'selling_price' => 'required|numeric|gt:0',
                'buying_price' => 'required|numeric|gt:0',
                'specification' => 'required',
                'material' => 'required',
                'weight' => 'required',
                'weight_type' => 'required',
                'style' => 'required',
                'wash_care' => 'required',
                'is_including_taxes' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }

            session()->forget('varient_product_image');
            session()->forget('product_images');
            DB::beginTransaction();
            $is_active = 2;
            if (!empty($post_data['save']))
                $is_active = 1;
            $originalString = $request->name ?? "";
            $lowercaseString = Str::lower($originalString);
            $slug = Str::slug($lowercaseString, '-');
            $category_id = $request->category_id;
            if (isset($request->sub_category_id)) {
                $category_id = $request->sub_category_id;
            }
            if (isset($request->child_category_id)) {
                $category_id = $request->child_category_id;
            }
            $relatedProducts = isset($request->Product_id) && is_array($request->Product_id) ? implode(',', $request->Product_id) : null;
            $collectionIds = isset($request->collection_ids) && is_array($request->collection_ids) ? implode(',', $request->collection_ids) : null;;
            $productTags =  isset($request->product_tags) && is_array($request->product_tags) ? implode(',', $request->product_tags) : null;

            $productArray = [
                'name' => $request->name,
                'slug' => $slug,
                'product_number' => '00',
                'sku' =>  $request->sku,
                'qty' => $request->qty,
                'hsn' => $request->hsn,
                'is_active' => $is_active,
                'product_type' => $request->product_type,
                'list_description' => $request->massage,
                'related_product_categores_id' => $request->categorys_id,
                'related_product_subcategory_id' => $request->subcategory_id,
                'related_products' => $relatedProducts,
                'bar_code' => $request->bar_code,
                'description' => $request->description,
                'specification' => $request->specification,
                'brand_id' => $request->brand_id,
                'category_id' => $category_id,
                'main_category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'buying_price' => $request->buying_price,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type,
                'selling_price' => $request->selling_price,
                'max_selling_units' => $request->max_selling_units,
                'min_selling_units' => $request->min_selling_units,
                'is_including_taxes' => $request->is_including_taxes,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'seo_content' => $request->seo_content,
                'material' => $request->material,
                'weight' => $request->weight,
                'weight_type' => $request->weight_type,
                'style' => $request->style,
                'country_origin' => $request->country_origin,
                'wash_care' => $request->wash_care,
                'product_tags' => $productTags,
                'collection_ids' => $collectionIds,
            ];
            //get last insertted id
            $product = Product::create($productArray);
            $lastInsertedId = $product->id;

            //saving variant color Images  when product type if configurable
            if ($request->product_type == 2) {
                if (isset($post_data['varient_product_image']) && !empty($post_data['varient_product_image'])) {
                    foreach ($post_data['varient_product_image'] as $colorid => $file) {
                        if ($file) {
                            $productImageArray = [
                                'product_id' => $lastInsertedId,
                                'color_id'  => $file['color_id'],
                                'image'  => $file['path'],
                                'is_front'  => $file['is_front'],
                                'is_back'  => $file['is_back'],
                            ];
                            ProductColorImage::create($productImageArray);
                        }
                    }
                }
            }

            //added product attributes
            $post_data = $request->all();
            if (!empty($request->attribute_ids)) {

                $attributeIds = $request->input('attribute_ids');
                $attributeValueIds = $request->input('attribute_value_ids');
                if (count($attributeIds) == count($attributeValueIds)) {
                    foreach ($attributeIds as $index => $attributeId) {
                        $attributeValueId = $attributeValueIds[$index];

                        // Here we can create records in the database
                        $product_attributes = new ProductAttribute();
                        $product_attributes->product_id = $lastInsertedId;
                        $product_attributes->attribute_id = $attributeId;
                        $product_attributes->save();

                        $product_attributes_values = new ProductAttributeValue();
                        $product_attributes_values->product_id = $lastInsertedId;
                        $product_attributes_values->product_attribute_id = $product_attributes->id;
                        $product_attributes_values->attribute_value_id = $attributeValueId;
                        $product_attributes_values->save();
                    }
                }
            }
            //end of product attributess

            //$variantProductImages = $request->file('variants_product_image');
            if ($request->product_type == 1) {


                if (isset($post_data['images']) && !empty($post_data['images'])) {
                    foreach ($post_data['images'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'] ?? 0;
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            $productColorImage->save();
                        }
                    }
                }

                if (isset($post_data['videos']) && !empty($post_data['videos'])) {
                    foreach ($post_data['videos'] as $colorid => $file) {
                        if ($file) {
                            $ProductColorVideo = new ProductColorVideo();
                            $ProductColorVideo->product_id = $lastInsertedId;
                            $ProductColorVideo->color_id = $file['color_id'] ?? 0;
                            $ProductColorVideo->video = $file['path'];
                            $ProductColorVideo->is_front = $file['is_front'];
                            $ProductColorVideo->is_back = $file['is_back'];
                            $ProductColorVideo->save();
                        }
                    }
                }

                if (!empty($request->simplevariant)) {

                    $productVariant = ProductVariant::create([
                        'variant_id' => $request->simplevariant,
                        'product_id' => $lastInsertedId,
                    ]);
                    $totalSimpleUnits = 0;
                    if (!empty($request->variant_valueas)) {
                        foreach ($request->variant_valueas as $variant_valueas_key => $variant_valueas) {
                            $simpleVarintMrp = $request->simple_mrp[$variant_valueas] ?? 0;
                            $simpleVarintSellPrice = $request->simple_units_selling_price[$variant_valueas] ?? 0;
                            $simpleVarintUnits = $request->simple_units[$variant_valueas] ?? 0;
                            $simple_varint_sku = $request->simple_varint_sku[$variant_valueas] ?? 0;

                            $variant = new ProductVariantCombination();
                            $variant->product_id = $lastInsertedId;
                            $variant->simple_varint_value   = $variant_valueas;
                            $variant->sku = $simple_varint_sku;
                            $variant->available_units = $simpleVarintUnits;
                            $variant->selling_price   =  $simpleVarintSellPrice;
                            $variant->variant_mrp   =  $simpleVarintMrp;
                            $variant->save();
                            $lastVariantId = $productVariant->id;
                            $totalSimpleUnits += $simpleVarintUnits;
                        }
                    }
                }
                SimpleVeriantValue::create(['product_id' => $lastInsertedId, 'variant_id' => $request->simplevariant, 'total_units' => $totalSimpleUnits, 'variant_values' => implode(',', $request->variant_valueas)]);
                // $variant = new ProductVariantCombination; 
                // $variant->product_id = $lastInsertedId;
                // if ($request->variant_type == 'nosizenocolor') {
                //     $variant->size_variant_value_id  = $request->variant_value;
                //     $productVariantNosize = new ProductVariant();
                //     $productVariantNosize->variant_id = 3;
                //     $productVariantNosize->product_id = $lastInsertedId;
                //     $productVariantNosize->save();
                // }
                // if ($request->variant_type == 'size') {
                //     $variant->size_variant_value_id  = $request->variant_value;
                //     $productVariantSize = new ProductVariant();
                //     $productVariantSize->variant_id = 2;
                //     $productVariantSize->product_id = $lastInsertedId;
                //     $productVariantSize->save();
                // }
                // if ($request->variant_type == 'color') {
                //     $variant->color_variant_value_id  = $request->variant_value;
                //     $productVariantColor = new ProductVariant();
                //     $productVariantColor->variant_id = 1;
                //     $productVariantColor->product_id = $lastInsertedId;
                //     $productVariantColor->save();
                // }
                // $variant->selling_price = $request->selling_price ?? 0;
                // $variant->available_units = 0;
                // $variant->selling_units = $request->max_selling_units ?? 0;
                // $variant->save();
            } else {
                if (isset($post_data['images']) && !empty($post_data['images'])) {
                    foreach ($post_data['images'] as $colorid => $file) {
                        if ($file) {
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $lastInsertedId;
                            $productColorImage->color_id = $file['color_id'];
                            $productColorImage->image = $file['path'];
                            $productColorImage->is_front = $file['is_front'];
                            $productColorImage->is_back = $file['is_back'];
                            $productColorImage->save();
                        }
                    }
                }
                // New code place here Start

                $no_of_varient_selected = 0;
                if (isset($post_data['added-variants-id']) && !empty($post_data['added-variants-id'])) {
                    $no_of_varient_selected = explode(',', $post_data['added-variants-id']);
                    //print_r($no_of_varient_selected);
                    foreach ($no_of_varient_selected as $key => $varient_id) {
                        $new_index_no = $key + 1;
                        // Here we inserting every selected product variant id
                        $productVariant = new ProductVariant();
                        $productVariant->variant_id = $varient_id;
                        $productVariant->product_id = $lastInsertedId;
                        $productVariant->save();

                        if (isset($no_of_varient_selected[$new_index_no])) {
                            $productVariantSize = new ProductVariant();
                            $productVariantSize->variant_id = 2;
                            $productVariantSize->product_id = $lastInsertedId;
                            $productVariantSize->save();
                        }
                        if (isset($post_data['color_variants_' . $new_index_no])) {
                            foreach ($post_data['color_variants_' . $new_index_no] as $variantValueId) {
                                $next_index_no = $new_index_no + 1; // here we are getting value 
                                //$next_index_no = $new_index_no;
                                //echo $variantValueId."<br>";
                                if (isset($post_data['color_variants_' . $next_index_no]) && count($post_data['color_variants_' . $next_index_no]) > 0) {
                                    foreach ($post_data['color_variants_' . $next_index_no] as $sizeVariantId) {
                                        $price =  $post_data['variant_selling_price'][$variantValueId][$sizeVariantId] ?? 0;
                                        $availableUnits =  $post_data['variant_available_unit'][$variantValueId][$sizeVariantId] ?? 0;
                                        //$maxSellingUnits =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? 0;
                                        $variantSku =  $post_data['variant_sku'][$variantValueId][$sizeVariantId] ?? "";
                                        $variantSellingPrice =  $post_data['variant_selling_unit'][$variantValueId][$sizeVariantId] ?? "";

                                        //echo "$varient_id = $variantValueId = $sizeVariantId = $price = $availableUnits = $variantSku = $variantSellingPrice<br>";

                                        $variant = new ProductVariantCombination();
                                        $variant->product_id = $lastInsertedId;
                                        $variant->color_variant_value_id  = $variantValueId;
                                        $variant->size_variant_value_id   = $sizeVariantId;
                                        $variant->sku = $variantSku;
                                        $variant->available_units = $availableUnits;
                                        $variant->selling_price   =  $variantSellingPrice;
                                        $variant->variant_mrp   =  $price;
                                        $variant->save();
                                        $lastVariantId = $variant->id;

                                        if (isset($productVariantSize) && !empty($productVariantSize)) {
                                            $productVariantValues = ProductVariantValue::where('product_veriant_id', $productVariantSize->id)->where('veriant_value_id',  $sizeVariantId)->first();
                                            if (empty($productVariantValues)) {
                                                $productVariantValue = new ProductVariantValue();
                                                $productVariantValue->product_veriant_id  = $productVariantSize->id;
                                                $productVariantValue->veriant_value_id = $sizeVariantId;
                                                $productVariantValue->save();
                                            }
                                        }
                                    }
                                }
                                if (!empty($productVariant)) {
                                    $productVariantValues = ProductVariantValue::where('product_veriant_id', $productVariant->id)->where('veriant_value_id',  $sizeVariantId)->first();
                                    if (empty($productVariantValues)) {
                                        $productVariantValue = new ProductVariantValue();
                                        $productVariantValue->product_veriant_id  = $productVariant->id;
                                        $productVariantValue->veriant_value_id = $sizeVariantId;
                                        $productVariantValue->save();
                                    }
                                }
                            }
                        }
                    }
                }
                // New code place here End

                if (5 > 7 && isset($post_data['color_variants']) && is_array($post_data) && (is_array($post_data['color_variants']) || is_array($post_data['color_variants']))) {
                    if ($request->variant_type == 'colour') {
                        $productVariantColor = new ProductVariant();
                        $productVariantColor->variant_id = 1;
                        $productVariantColor->product_id = $lastInsertedId;
                        $productVariantColor->save();
                    }

                    if ($request->variant_type == 'nosizenocolor') {
                        $productVariantNosize = new ProductVariant();
                        $productVariantNosize->variant_id = 3;
                        $productVariantNosize->product_id = $lastInsertedId;
                        $productVariantNosize->save();
                    }

                    if ($request->variant_type == 'size') {
                        $productVariantSize = new ProductVariant();
                        $productVariantSize->variant_id = 2;
                        $productVariantSize->product_id = $lastInsertedId;
                        $productVariantSize->save();
                    }


                    foreach ($post_data['color_variants'] as $colorVariantId) {
                        if (isset($post_data['size_variants']) && count($post_data['size_variants']) > 0) {
                            foreach ($post_data['size_variants'] as $sizeVariantId) {
                                $price =  $post_data['variant_selling_price'][$colorVariantId][$sizeVariantId] ?? 0;
                                $availableUnits =  $post_data['variant_available_unit'][$colorVariantId][$sizeVariantId] ?? 0;
                                //$maxSellingUnits =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? 0;
                                $variantSku =  $post_data['variant_sku'][$colorVariantId][$sizeVariantId] ?? "";
                                $variantSellingPrice =  $post_data['variant_selling_unit'][$colorVariantId][$sizeVariantId] ?? "";

                                $variant = new ProductVariantCombination();
                                $variant->product_id = $lastInsertedId;
                                $variant->color_variant_value_id  = $colorVariantId;
                                $variant->size_variant_value_id   = $sizeVariantId;
                                $variant->sku = $variantSku;
                                $variant->available_units = $availableUnits;
                                $variant->selling_price   =  $variantSellingPrice;
                                $variant->variant_mrp   =  $price;
                                $variant->save();
                                $lastVariantId = $variant->id;

                                if (!empty($productVariantSize)) {
                                    $productVariantValues = ProductVariantValue::where('product_veriant_id', $productVariantSize->id)->where('veriant_value_id',  $sizeVariantId)->first();
                                    if (empty($productVariantValues)) {
                                        $productVariantValue = new ProductVariantValue();
                                        $productVariantValue->product_veriant_id  = $productVariantSize->id;
                                        $productVariantValue->veriant_value_id = $sizeVariantId;
                                        $productVariantValue->save();
                                    }
                                }
                            }
                        }
                        if (!empty($colorVariantId) && !empty($productVariantColor)) {

                            $productVariantValue = new ProductVariantValue();
                            $productVariantValue->product_veriant_id = $productVariantColor->id;
                            $productVariantValue->veriant_value_id = $colorVariantId;
                            $productVariantValue->save();
                        }
                    }
                }
            }

            if ($request->Main_images) {
                $ProductId = $product->id;
                $GELLERYIMAGES = $request->file('gallery_images');
                foreach ($request->file('Main_images') as $colorid => $file) {
                    if ($file) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs("main/{$ProductId}", $fileName, 'public');
                        $productColorImage = new ProductColorImage();
                        $productColorImage->product_id = $ProductId;
                        $productColorImage->color_id = $colorid;
                        $productColorImage->image = $filePath;
                        $productColorImage->is_front = 1;
                        $productColorImage->save();
                    }
                    foreach ($GELLERYIMAGES[$colorid] as $newid => $files) {
                        if ($files) {
                            $fileName = time() . '_' . $files->getClientOriginalName();
                            $filePath = $files->storeAs("gallery/{$ProductId}", $fileName, 'public');
                            $productColorImage = new ProductColorImage();
                            $productColorImage->product_id = $ProductId;
                            $productColorImage->color_id = $colorid;
                            $productColorImage->image = $filePath;
                            $productColorImage->is_front = 0;
                            $productColorImage->save();
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin-product-list')->with(['success' => "Product Added Successfully."]);
        } else {
            //print_r(session()->get('product_images')); die;
            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->get();
            $brands = Brand::where('is_active', 1)->where('is_deleted', 0)->get();
            $producttags = Tag::where('is_active', 1)->where('is_deleted', 0)->get();
            $variants = Variant::where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');
            $attributes = Attribute::where('is_active', 1)->where('is_deleted', 0)->get();

            // $colors = VariantValue::where('variant_id', 1)->get();
            // $sizes =  VariantValue::where('variant_id', 2)->orderBy('id', 'ASC')->get();

            // start color and size field get

            $variants_color = Variant::where('name', 'color')->pluck('id');
            $variants_size = Variant::where('name', 'size')->pluck('id');

            $colors = VariantValue::whereIn('variant_id', $variants_color)->get();
            $sizes = VariantValue::whereIn('variant_id', $variants_size)->orderBy('id', 'ASC')->get();

            // End color and size field get


            $attributesdatain = Variant::where('type', 2)->get();
            $attributesdatain = Variant::where('type', '!=', 0)->where('is_active', 1)->get();
            $variantData = [];
            foreach ($attributesdatain as $data) {
                $variantData[$data->id] = VariantValue::where('variant_id', $data->id)->pluck('name', 'id');
            }

            #echo "<pre>";print_r($attributesdatain);exit;

            $subcategory = Category::get();
            $subproducts = Product::get();
            $attributesdata = Variant::where('type', 2)->get();

            if ($request->session()->has('currentProductId') || session()->has('variants') || session()->has('attributes') || session()->has('product_images')) {
                $request->session()->forget('currentProductId');
                session()->forget('variants');
                session()->forget('attributes');
                session()->forget('product_images');
            }
            $type = 'create';
            $variant_values = [];
            $collections = ProductCollection::pluck('title', 'id')->toArray();
            $simpleVariants = Variant::where('is_active', 1)->where('is_deleted', 0)->where('type', 1)->pluck('name', 'id');
            return view('admin.product_new.create-new-demo', compact('attributesdatain', 'categories', 'attributesdata', 'variantData', 'subcategory', 'subproducts', 'brands', 'producttags', 'type', 'variants', 'variant_values', 'attributes', 'colors', 'sizes', 'collections', 'simpleVariants'));
        }
    }

    public function copy($id)
    {
        $data = Product::find($id);
        if ($data) {
            $originalString = $data->name ?? "";
            $lowercaseString = Str::lower($originalString);
            $uniqueNumber = time();
            $slug = Str::slug($lowercaseString . '-' . $uniqueNumber, '-');
            $newData = new Product();
            $newData->name = $data->name . '(Copy)';
            $newData->slug = $slug;
            $newData->sku = $data->sku;
            $newData->hsn = $data->hsn;
            $newData->bar_code = $data->bar_code;
            $newData->description = $data->description;
            $newData->specification = $data->specification;
            $newData->material = $data->material;
            $newData->weight = $data->weight;
            $newData->weight_type = $data->weight_type;
            $newData->style = $data->style;
            $newData->country_origin = $data->country_origin;
            $newData->wash_care = $data->wash_care;
            $newData->return_exchange = $data->return_exchange;
            $newData->product_number = $data->product_number;
            $newData->category_id  = $data->category_id;
            $newData->sub_category_id = $data->sub_category_id;
            $newData->child_category_id = $data->child_category_id;
            $newData->brand_id = $data->brand_id;
            $newData->short_description = $data->short_description;
            $newData->long_description = $data->long_description;
            $newData->return_policy = $data->return_policy;
            $newData->seller_information = $data->seller_information;
            $newData->meta_title = $data->meta_title;
            $newData->meta_description = $data->meta_description;
            $newData->meta_keywords = $data->meta_keywords;
            $newData->seo_content = $data->seo_content;
            $newData->buying_price = $data->buying_price;
            $newData->discount = $data->discount;
            $newData->discount_type = $data->discount_type;
            $newData->selling_price = $data->selling_price;
            $newData->is_including_taxes = $data->is_including_taxes;
            $newData->in_stock = $data->in_stock;
            $newData->is_featured = $data->is_featured;
            $newData->is_new_arrivals = $data->is_new_arrivals;
            $newData->is_active = $data->is_active;
            $newData->is_deleted = $data->is_deleted;
            if ($newData->save()) {
                session()->flash('success', 'New entry saved successfully.');
                return redirect()->back();
            } else {
                session()->flash('error', 'Data not found for the given ID.');
                return redirect()->back();
            }
        }
    }

    public function product_gallery_img_delete(Request $request)
    {
        $id = $request->input('id');
        $imgid = $request->input('imgId');
        $typ = $request->input('typ');

        if ($typ == 'gallery') {
            $ProductColorImage = ProductColorImage::where('id', $imgid)->where('color_id', $id)->where('is_front', 0)->first();
            $ProductColorImage->delete();
            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
        } else {
            $ProductColorImage = ProductColorImage::where('id', $imgid)->where('color_id', $id)->where('is_front', 1)->first();
            $ProductColorImage->delete();
            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
        }
    }

    public function uploadImagesNew(Request $request)
    {
        if (!empty($request->file)) {
            $successMsg = "Images uploaded successfully.";
            $images = session()->get('product_images', []);
            $videos = session()->get('product_videos', []);
            $post = $request->all();

            if (isset($post['color_id']) && !empty($request->all('color_id'))) {
                $colorIds = $post['color_id'];
                $images = session()->get('varient_product_image', []);
            } else {
                $colorIds = "";
            }

            foreach ($request->file as $imageKey => $imageVal) {
                if (!empty($imageVal)) {
                    $extension = $imageVal->getClientOriginalExtension();
                    $mimeType = $imageVal->getMimeType();
                    $isImage = str_starts_with($mimeType, 'image/');
                    $isVideo = str_starts_with($mimeType, 'video/');

                    $fileName = ($isImage ? 'product_image-' : 'product_video-') . time() . '-' . $imageKey . '.' . $extension;
                    $folderPath = config('constant.PRODUCT_IMAGE_ROOT_PATH');
                    #echo $folderPath . '===';
                    #$folderPath = public_path('uploads/products/');
                    #echo $folderPath;exit;
                    if ($isImage) {
                        $img = Image::read($imageVal);
                        $folderPaththumbnail = $folderPath . 'thumbnail/';
                        $folderPathmedium = $folderPath . 'medium/';
                        $folderPathlarge = $folderPath . 'large/';

                        #echo $folderPathlarge;exit;
                        // create 'thumbnail' new directory if not exist.
                        if (!File::exists($folderPaththumbnail)) {
                            File::makeDirectory($folderPaththumbnail, $mode = 0777, true);
                        }

                        // create 'medium' new directory if not exist.
                        if (!File::exists($folderPathmedium)) {
                            File::makeDirectory($folderPathmedium, $mode = 0777, true);
                        }

                        // create 'large' new directory if not exist.
                        if (!File::exists($folderPathlarge)) {
                            File::makeDirectory($folderPathlarge, $mode = 0777, true);
                        }

                        $originalPath = $folderPath . $fileName;

                        try {
                            if ($img->save($folderPath . $fileName)) {
                                // Resize for thumbnail
                                resizeImage($originalPath, 100, 131, $folderPaththumbnail . $fileName);
                                // Resize for medium
                                resizeImage($originalPath, 306, 479, $folderPathmedium . $fileName);
                                // Resize for large
                                resizeImage($originalPath, 746, 1086, $folderPathlarge . $fileName);

                                if (isset($colorIds)) {
                                    $image['name'] = $fileName;
                                    $image['path'] = $fileName;
                                    $image['ext'] = $extension;
                                    $image['is_front'] = 0;
                                    $image['is_back'] = 0;
                                    $image['color_id'] = $colorIds;
                                    $images[] = $image;
                                } else {
                                    $image['name'][$colorIds[$imageKey]] = $fileName;
                                    $image['path'] = $fileName;
                                    $image['ext'] = $extension;
                                    $image['is_front'] = 0;
                                    $image['is_back'] = 0;
                                    $images[] = $image;
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('Error while saving image: ' . $e->getMessage());
                        }
                    } elseif ($isVideo) {
                        $originalPath = $folderPath . $fileName;
                        if ($imageVal->move($folderPath, $fileName)) {
                            $video['name'] = $fileName;
                            $video['path'] = $fileName;
                            $video['ext'] = $extension;
                            $video['color_id'] = $colorIds;
                            $videos[] = $video;
                        }
                    }
                }
            }

            #echo "<pre>";print_r($videos);exit;

            if ($colorIds) {
                session()->put('varient_product_image', $images);
            } else {
                session()->put('product_images', $images);
            }
            session()->put('product_videos', $videos);
            $getData = View::make('admin.product_new.load-images', compact('images', 'videos', 'colorIds'))->render();
            $response = [
                "status" => "success",
                "msg" => trans($successMsg),
                "data" => $getData,
                //'images' => $images, 
                "http_code" => 200,
            ];
            return Response::json($response, 200);
        } else {
            $response = [
                "status" => "error",
                "msg" => trans("Something went wrong"),
                "data" => (object) [],
                "http_code" => 500,
            ];
            return Response::json($response, 500);
        }
    }




    // public function getImageData(Request $request)
    // {
    //     if (!empty($request->all())) {

    //         $post = $request->all(); 
    //         if(isset($post['color_id']) && !empty($request->all('color_id'))){                
    //             $colorIds = $post['color_id'];
    //         }else{
    //             $colorIds="";
    //         }
    //         $images = session()->get('product_images'); 
    //         if(!empty(session()->get('product_images'))){
    //             $images = session()->get('images');
    //         } else {
    //             $images = ProductColorImage::where('product_id', $request['product_id'])->where('color_id',$request['color_id'])->get();
    //         }
    //         $getData = View::make('admin.product_new.load-images', compact('images','colorIds'))->render();
    //         $response = array();
    //         $response["status"] =true;
    //         $response["msg"] = trans("Got Images");
    //         $response["data"] = $getData;
    //         $response["http_code"] = 200;
    //         return Response::json($response, 200);
    //     } else {
    //         $response = array();
    //         $response["status"] = "error";
    //         $response["msg"] = trans("Something went wrong");
    //         $response["data"] = (object) array();
    //         $response["http_code"] = 500;
    //         return Response::json($response, 500);
    //     }
    // }

    public function getImageData(Request $request)
    {
        if (!empty($request->all())) {

            $images = session()->get('product_images');
            $post =   $request->all();
            if (isset($post['color_id']) && !empty($request->all('color_id'))) {
                $colorIds = $post['color_id'];
                $images = session()->get('varient_product_image');
            } else {
                $colorIds = "";
                $images = session()->get('product_images');
            }
            $getData = View::make('admin.product_new.load-images', compact('images', 'colorIds'))->render();

            $response = array();
            $response["status"] = true;
            $response["msg"] = trans("Got Images");
            $response["data"] = $getData;
            $response["http_code"] = 200;
            return Response::json($response, 200);
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("Something went wrong");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function productdeleteImage(Request $request)
    {
        $imagePath = $request->input('name');
        // Define the full path
        //$fullPath = public_path('uploads/products/' . $imagePath);
        $fullPath = config('constant.PRODUCT_IMAGE_ROOT_PATH') . $imagePath;

        // Check if the file exists and delete it
        if (file_exists($fullPath)) {
            unlink($fullPath); // Delete the file from the folder
        }

        // Remove the image record from the database
        ProductColorImage::where('image', $imagePath)->delete();

        return response()->json(['status' => 'success', 'success' => 'Image deleted successfully.']);
    }

    public function deleteImageNewBackup(Request $request)
    {
        if (session()->has('product_images')) {
            $productImages = session()->get('product_images');

            if (!empty($productImages)) {
                foreach ($productImages as $key => $image) {
                    if ($image['path'] == $request->name) {
                        unset($productImages[$key]);
                        // $filePath = public_path('public/uploads/products').'/'. $request->name;
                        // if (File::exists($filePath)) {
                        //     File::delete($filePath);
                        //     unset($productImages[$key]);
                        // }
                    }
                }
                session()->put('product_images', $productImages);
                $response = array();
                $response["status"] = "success";
                $response["msg"] = trans("Image deleted successfully.");
                $response["data"] = (object) array();
                $response["http_code"] = 200;
                return Response::json($response, 200);
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("Image not found");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else if (session()->has('varient_product_image')) {
            $productImages = session()->get('varient_product_image');
            if (!empty($productImages)) {
                // dd($request->name);
                foreach ($productImages as $key => $image) {
                    if ($image['path'] == $request->name) {
                        unset($productImages[$key]);
                        $filePath = public_path('public/uploads/products') . '/' . $request->name;
                        $thumbnailPath = public_path('public/uploads/products') . '/thumbnail_' . $request->name;
                        if (File::exists($filePath)) {
                            File::delete($filePath);
                            unset($productImages[$key]);
                        }
                        if (File::exists($thumbnailPath)) {
                            File::delete($thumbnailPath);
                        }
                    }
                }
                session()->put('varient_product_image', $productImages);
                $response = array();
                $response["status"] = "success";
                $response["msg"] = trans("Image deleted successfully.");
                $response["data"] = (object) array();
                $response["http_code"] = 200;
                return Response::json($response, 200);
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("Image not found");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("Something went wrong");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function deleteImageNew(Request $request)
    {
        // if (session()->has('product_images')) {
        //     $productImages = session()->get('product_images'); dd($productImages);
        //     if (!empty($productImages)) {
        //         foreach ($productImages as $key => $image) {
        //             if ($image['path'] == $request->name) {
        //                 unset($productImages[$key]);
        //                 // $filePath = public_path('public/uploads/products').'/'. $request->name;
        //                 // if (File::exists($filePath)) {
        //                 //     File::delete($filePath);
        //                 //     unset($productImages[$key]);
        //                 // }
        //             }
        //         }
        //         session()->put('product_images', $productImages);
        //         $response = array();
        //         $response["status"] = "success";
        //         $response["msg"] = trans("Image deleted successfully.");
        //         $response["data"] = (object) array();
        //         $response["http_code"] = 200;
        //         return Response::json($response, 200);
        //     } else {
        //         $response = array();
        //         $response["status"] = "error";
        //         $response["msg"] = trans("Image not found");
        //         $response["data"] = (object) array();
        //         $response["http_code"] = 500;
        //         return Response::json($response, 500);
        //     }
        // } 

        if (session()->has('varient_product_image')) {

            $productImages = session()->get('varient_product_image');
            if (!empty($productImages)) {
                foreach ($productImages as $key => $image) {
                    if ($image['path'] == $request->name) {
                        unset($productImages[$key]);
                        $filePath = public_path('public/uploads/products') . '/' . $request->name;
                        $thumbnailPath = public_path('public/uploads/products') . '/thumbnail_' . $request->name;
                        if (File::exists($filePath)) {
                            File::delete($filePath);
                            unset($productImages[$key]);
                        }
                        if (File::exists($thumbnailPath)) {
                            File::delete($thumbnailPath);
                        }
                    }
                }
                session()->put('varient_product_image', $productImages);
                $response = array();
                $response["status"] = "success";
                $response["msg"] = trans("Image deleted successfully.");
                $response["data"] = (object) array();
                $response["http_code"] = 200;
                return Response::json($response, 200);
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("Image not found");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("Something went wrong");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function deleteItem(Request $request)
    {
        $type = $request->type;
        if (session()->has($type)) {
            $data = session()->get($type);
            if (!empty($data)) {
                if ($type == 'variants') {
                    $ids = explode('-', $request->name);
                    if (count($ids) == 2) {
                        unset($data[$ids[0]]['value'][$ids[1]]);
                    }
                    $variant_table = view('admin.product_new.variants-table', ['variant_values' => $data])->render();
                } else {
                    $id = $request->name;
                    unset($data[$id]);
                    $t = 'save_attribute';
                    $variant_table = view('admin.product_new.variants-table', ['variant_values' => $data, 'type' => $t])->render();
                }
                session()->put($type, $data);
                $response = array();
                $response["status"] = "success";
                $response["type"] = $type;
                $response["msg"] = trans("Item deleted successfully.");
                $response["data"] = $variant_table;
                $response["http_code"] = 200;
                return Response::json($response, 200);
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("Item not found");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("Something went wrong");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function updateImageData(Request $request)
    {
        if (session()->has('product_images') && !empty(session()->get('product_images'))) {
            $productImages = session()->get('product_images');
            if (!empty($productImages)) {
                if (!empty($request->id) && !empty($request->type)) {
                    $id = base64_decode($request->id);
                    foreach ($productImages as $key => $image) {
                        $productImages[$key]['is_' . $request->type] = 0;
                        if ($key == $id) {
                            $productImages[$key]['is_' . $request->type] = 1;
                        }
                    }
                    session()->put('product_images', $productImages);
                    $response = array();
                    $response["status"] = "success";
                    $response["msg"] = trans("Updated successfully.");
                    $response["data"] = (object) array();
                    $response["http_code"] = 200;
                    return Response::json($response, 200);
                } else {
                    $response = array();
                    $response["status"] = "error";
                    $response["msg"] = trans("Something went wrong.");
                    $response["data"] = (object) array();
                    $response["http_code"] = 500;
                    return Response::json($response, 500);
                }
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("There are no images found.");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } elseif (session()->has('varient_product_image')) {
            $productImages = session()->get('varient_product_image');
            if (!empty($productImages)) {
                if (!empty($request->id) && !empty($request->type)) {
                    $id = base64_decode($request->id);
                    foreach ($productImages as $key => $image) {
                        $productImages[$key]['is_' . $request->type] = 0;
                        if ($key == $id) {
                            $productImages[$key]['is_' . $request->type] = 1;
                        }
                    }
                    session()->put('varient_product_image', $productImages);
                    $response = array();
                    $response["status"] = "success";
                    $response["msg"] = trans("Updated successfully.");
                    $response["data"] = (object) array();
                    $response["http_code"] = 200;
                    return Response::json($response, 200);
                } else {
                    $response = array();
                    $response["status"] = "error";
                    $response["msg"] = trans("Something went wrong.");
                    $response["data"] = (object) array();
                    $response["http_code"] = 500;
                    return Response::json($response, 500);
                }
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("There are no images found.");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("There are no images found.");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function updateItem(Request $request)
    {
        dd('1');
        $type = $request->type;
        if (session()->has($type)) {
            $data = session()->get($type);
            if (!empty($data)) {
                if ($type == 'variants') {
                    $ids = explode('-', $request->name);
                    if (count($ids) == 2) {
                        $data[$ids[0]]['value'][$ids[1]]['price'] = $request->price;
                        $data[$ids[0]]['value'][$ids[1]]['available'] = $request->available;
                    }
                    $variant_table = view('admin.product_new.variants-table', ['variant_values' => $data])->render();
                }
                session()->put($type, $data);
                $response = array();
                $response["status"] = "success";
                $response["type"] = $type;
                $response["msg"] = trans("Item updated successfully.");
                $response["data"] = $variant_table;
                $response["http_code"] = 200;
                return Response::json($response, 200);
            } else {
                $response = array();
                $response["status"] = "error";
                $response["msg"] = trans("Item not found");
                $response["data"] = (object) array();
                $response["http_code"] = 500;
                return Response::json($response, 500);
            }
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = trans("Something went wrong");
            $response["data"] = (object) array();
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function add_attribute(Request $request)
    {
        $type = $request->type;
        $item = $request->item;
        if ($type == 'attribute_value') {
            $attribute_id = $request->attribute_id;
            $attribute = AttributeValue::where(['attribute_id' => $attribute_id, 'name' => $item])->first();
            if (empty($attribute)) {
                $attribute = new AttributeValue();
                $attribute->attribute_id = $attribute_id;
                $attribute->name = $item;
                $attribute->save();
            }
        } else {
            $attribute = Attribute::where(['name' => $item])->first();
            if (!empty($attribute)) {
                $attribute->is_active = 1;
                $attribute->is_deleted = 0;
            } else {
                $attribute = new Attribute();
                $attribute->name = $item;
            }
            $attribute->save();
        }

        $response = array();
        $response["status"] = "success";
        $response["type"] = $type;
        $response["msg"] = trans("");
        $response["data"] = $attribute->id;
        $response["name"] = $attribute->name;
        $response["color"] = "";
        $response["http_code"] = 200;
        return Response::json($response, 200);
    }

    public function getAttributeValues(Request $request)
    {
        $attributeId = $request->query('attribute_id');
        $attributeValues = AttributeValue::where('attribute_id', $attributeId)->get();

        return response()->json(['attributeValues' => $attributeValues]);
    }

    // public function saveAttribute(Request $request)
    // {

    //     $request->validate([
    //         'attributeName' => 'required|string|max:255',
    //         'attributeValue' => 'nullable|string|max:255',
    //     ]);
    //     $attribute_id = '';
    //     $attribute_name = '';
    //     $attribute = Attribute::where('id', $request->attributeName)->orWhere('name', $request->attributeName)->first();
    //     // dd($attribute);
    //     if (empty($attribute)) {
    //         $attribute = new Attribute();
    //         $attribute->name = $request->attributeName;
    //         $attribute->save();
    //         $attribute_id = $attribute->id;
    //         $attribute_name = $attribute->name;
    //     }

    //     $existingValue = AttributeValue::where('attribute_id', $attribute->id)
    //     ->where('name', $request->attributeValue)
    //     ->first();
    //     if ($existingValue) {
    //         return response()->json([
    //             'msg' => 'This attribute value already exists.',
    //         ]);
    //     }

    //     $attributeValue = new AttributeValue();
    //     $attributeValue->name = $request->attributeValue;
    //     $attributeValue->attribute_id = $attribute->id;
    //     $attributeValue->save();

    //     // Return success response
    //     return response()->json([
    //         'attribute_id' => $attribute_id,
    //         'attribute_name' => $attribute_name,
    //         'attribute_value_id' => $attributeValue->id,
    //         'attribute_value' => $attributeValue->value,
    //         'msg' => 'Added successfully',
    //     ]);
    // }

    public function saveAttribute(Request $request)
    {
        $request->validate([
            'attributeId' => 'required',
            'attributeValues' => 'required|array',
            'attributeValues.*' => 'nullable|string|max:255',
        ]);

        $attribute_id = '';
        $attribute_name = '';

        // Check if attribute exists by ID or name
        $attribute = Attribute::where('id', $request->attributeId)
            ->orWhere('name', $request->attributeId)
            ->first();

        if (empty($attribute)) {
            $attribute = new Attribute();
            $attribute->name = $request->attributeId;
            $attribute->save();
        }

        $attribute_id = $attribute->id;
        $attribute_name = $attribute->name;

        $saved_values = [];
        $duplicate_values = [];

        foreach ($request->attributeValues as $value) {
            if (empty($value)) {
                continue;
            }

            $existingValue = AttributeValue::where('attribute_id', $attribute->id)
                ->where('name', $value)
                ->first();

            if ($existingValue) {
                $duplicate_values[] = $value;
                continue;
            }

            $attributeValue = new AttributeValue();
            $attributeValue->name = $value;
            $attributeValue->attribute_id = $attribute->id;
            $attributeValue->save();

            $saved_values[] = [
                'id' => $attributeValue->id,
                'name' => $attributeValue->name,
            ];
        }

        return response()->json([
            'success' => true,
            'attribute_id' => $attribute_id,
            'attribute_name' => $attribute_name,
            'saved_values' => $saved_values,
            'duplicate_values' => $duplicate_values,
            'msg' => 'Processed successfully',
        ]);
    }


    public function ajaxsubcategory(Request $request)
    {
        $category_id = $request->input('catid');
        $subcategories = Category::where('parent_id', $category_id)->get();

        return response()->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }

    public function ajaxgetrelatedsubcategories(Request $request)
    {
        $category_ids = $request->input('category_ids');

        $subcategories = Category::where('parent_id', $category_ids)->get();

        return response()->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }

    public function ajaxgetCategoriesVarient(Request $request)
    {
        $category_id = $request->input('category_id');
        $variantsData = CategoryVariant::select('variants.id', 'variants.name')
            ->leftJoin('variants', 'category_variants.variant_id', '=', 'variants.id')
            ->where(function ($query) use ($category_id) {
                $query->where('category_variants.category_id', $category_id);
            })
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'variantsData' => $variantsData
        ]);
    }


    public function ajaxgetCategoriesAttribute(Request $request)
    {
        $category_id = $request->input('category_id');
        $attributesData = CategoryAttribute::select('attributes.id', 'attributes.name')
            ->leftJoin('attributes', 'category_attributes.attribute_id', '=', 'attributes.id')
            ->where(function ($query) use ($category_id) {
                $query->where('category_attributes.category_id', $category_id);
            })
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'attributesData' => $attributesData
        ]);
    }

    public function ajaxgetproduct(Request $request)
    {
        $subcategory_id = $request->input('subcatid');
        $subproducts = Product::where(['main_sub_category_id' => $subcategory_id, 'is_active' => 1])->select('id', 'name')->get();
        //dd($subproducts);
        return response()->json([
            'success' => true,
            'subproducts' => $subproducts
        ]);
    }


    public function ajaxgetchildcategory(Request $request)
    {
        $subcategory_id = $request->input('subctgids');
        $childcat = Category::where('parent_id', $subcategory_id)->get();
        //dd($subproducts);
        return response()->json([
            'success' => true,
            'childcat' => $childcat
        ]);
    }

    public function review($token)
    {
        $prdId = decrypt($token);
        try {

            $result = [];
            $colorArray = [];
            $productReviews = UserReview::where('product_id', $prdId)->where('is_deleted', '0')->get();
            $totalResults = $productReviews->count();
            return view('admin.products.review', compact('productReviews', 'result', 'totalResults'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function editReview(Request $request, $token = null)
    {
        try {
            $reviewId = '';
            if (!empty($token)) {
                $reviewId = base64_decode($token);
                $review = UserReview::find($reviewId);
                $user = User::where('is_deleted', 0)->where('is_active', 1)->select('id', 'name')->get();
                // echo "<pre>"; print_r($categoryTaxes); die;
                return View("admin.products.reviewedit", compact('review', 'user'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }


    public function updateReview(Request $request, $encodedReviewId, $encodedProductId)
    {
        try {
            // Log the request data
            Log::info('Request Data:', $request->all());

            // Decode IDs and find the review
            $reviewId = base64_decode($encodedReviewId);
            $productId = base64_decode($encodedProductId);
            $review = UserReview::find($reviewId);

            if (!$review) {
                return redirect()->route('admin-product-review', ['token' => encrypt($productId)])
                    ->with('error', 'Review not found.');
            }

            // Validate the incoming request
            $request->validate([
                'rating' => 'required|integer|between:1,5',
                'title' => 'required|string|min:5',
                'review' => 'required|string|max:1000',
            ]);

            // Update and save the review
            $review->title = $request->input('title');
            $review->rating = $request->input('rating');
            $review->review = $request->input('review');
            $oldImages = json_decode($request->old_images, true) ?? [];

            $uploadedImages = [];
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $extension = $image->getClientOriginalExtension();
                    $originalName = $image->getClientOriginalName();
                    $fileName = time() . '-' . uniqid() . '.' . $extension;

                    // Folder: e.g. "OCT2025/"
                    $folderName = strtoupper(date('M') . date('Y')) . '/';
                    $folderPath = config('constant.REVIEW_IMAGE_ROOT_PATH') . $folderName;

                    // Ensure directory exists
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, 0777, true);
                    }

                    // Move file
                    if ($image->move($folderPath, $fileName)) {
                        $uploadedImages[] = $folderName . $fileName;
                    }
                }
            }
            $allImages = array_merge($oldImages, $uploadedImages);
            $review->image = json_encode(array_values($allImages));
            $review->save();

            session()->flash('flash_notice', trans("Review updated successfully."));
            return redirect()->route('admin-product-review', ['token' => encrypt($productId)]);
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function reviewdeleteImage(Request $request)
    {
        $review = UserReview::find($request->review_id);
        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Review not found']);
        }

        $images = json_decode($review->image, true);
        $imageToDelete = $request->image;

        // Remove from array
        $images = array_filter($images, fn($img) => $img !== $imageToDelete);

        // Update DB
        $review->image = json_encode(array_values($images));
        $review->save();

        // Optional: delete from storage if needed
        $path = public_path('uploads/reviews/' . $imageToDelete);
        if (file_exists($path)) {
            unlink($path);
        }

        return response()->json(['success' => true]);
    }


    public function changeStatusReview($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Review has been actvated successfully");
        } else {
            $statusMessage = trans("Review has been deactivated successfully");
        }
        $review = UserReview::find($modelId);
        if ($review) {
            $currentStatus = $review->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $review->is_active = $NewStatus;
            $ResponseStatus = $review->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

    public function reviewDelete($encodedReviewId, $encodedProductId)
    {
        try {
            // $reviewId = '';
            // if (!empty($encodedReviewId)) {
            //     $reviewId = base64_decode($encodedReviewId);
            // }
            $reviewId = base64_decode($encodedReviewId);
            $productId = base64_decode($encodedProductId);
            $review = UserReview::find($reviewId);
            if (empty($review)) {
                return redirect()->route('admin-product-review', ['token' => base64_encode($productId)]);
            }
            if ($review) {
                UserReview::where('id', $reviewId)->update(array(
                    'is_deleted' => 1
                ));
                // CategoryVariant::where('category_id',$reviewId)->delete();
                // CategorySpecification::where('category_id',$reviewId)->delete();

                Session()->flash('flash_notice', trans("Review has been removed successfully."));
            }
            return back();
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function getAttributes()
    {
        $attributes = Attribute::where('is_active', 1)->where('is_deleted', 0)->get();
        return $attributes;
    }


    public function getVariantReleatedProduct(Request $request)
    {
        $variant_ids = $request->input('variant');
        $variantValues = $request->input('variant_values');
        $variantTypes = $request->input('variant_type');

        $totalVariant = 1;

        if (!$variantValues) {
            return response()->json([
                'success' => true,
                'variantHtml' => "",
                'totalProductVariant' => 0
            ]);
        }
        foreach ($variantValues as $index => $values) {
            if ($index == 0) continue;
            if (!empty($values)) {
                $totalVariant *= count($values);
            }
        }



        $available_variants = $this->generate_available_options($variant_ids, $variantValues);
        $split_variants = array_chunk($available_variants, ceil(count($available_variants) / count($variantValues[0])));

        if (count($split_variants) === 1) {
            $split_variants[1] = [];
        }

        $html = '';

        if (isset($variantValues[0])) {
            foreach ($variantValues[0] as $key => $variantValueId) {
                $variant = Variant::find($variant_ids[0]);
                $catVariant = VariantValue::find($variantValueId);

                $html .= '<div class="card mb-4 shadow-sm variant_group_row" id="variant_' . $catVariant->id . '">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <input class="form-check-input me-2" 
                                type="radio" 
                                required
                                value="' . $catVariant->id . '"
                                name="main_variant" 
                                id="variant_' . $catVariant->id . '" 
                                onchange="setMainProduct()">
                            <label for="variant_' . $catVariant->id . '" class="fw-bold mb-0">
                                Is this primary: ' . $catVariant->name . ' — ' . $totalVariant . ' Variants
                            </label>
                        </div>
                        <span class="text-muted">Total Qty: 0</span>
                    </div>

                    <div class="card-body">
                        <div class="row align-items-start mb-4">
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-secondary image_upload_button"
                                    data-bs-toggle="modal" data-bs-target="#uploadModal_' . $key . '">
                                    <div class="text-center">
                                        <div class="fs-2 fw-bold">+</div>
                                        <div class="small">Add Images & Video</div>
                                    </div>
                                </button>
                            </div>
                            <div class="col">
                                <div class="image-thumbnails d-flex flex-wrap gap-2 mb-2"></div>
                                <div class="video-thumbnails d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        <!-- Upload Modal -->
                        <div class="modal fade" id="uploadModal_' . $key . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content p-3">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Files for Group</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Select Images</label>
                                            <input type="file" name="variant_images[' . $catVariant->id . '][]" accept="image/*" multiple
                                                onchange="previewImages(event, ' . $catVariant->id . ')" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Select Video</label>
                                            <input type="file" name="variant_video[' . $catVariant->id . ']" accept="video/*"
                                                onchange="previewVideo(event, ' . $catVariant->id . ')" class="form-control">
                                        </div>
                                        <hr/>
                                        <h6 class="fw-bold mb-2">Image Preview</h6>
                                        <div id="preview_images_' . $catVariant->id . '" class="d-flex flex-wrap gap-2"></div>
                                        <h6 class="fw-bold mt-4 mb-2">Video Preview</h6>
                                        <div class="d-flex flex-wrap gap-2" id="preview_video_' . $catVariant->id . '"></div>
                                    </div>
                                </div>
                            </div>
                        </div>';

                // Table Start
                $html .= '<div class="table-responsive mt-3">
                    <table class="table table-bordered table-striped variant-combo-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Variant Name</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Sale Price</th>
                                <th>Discount Type</th>
                                <th>Discount</th>
                                <th>Qty</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($split_variants[$key] as $comboString) {
                    $variantValueIds = explode('_', $comboString);
                    $names = [];

                    foreach ($variantValueIds as $variantValueId) {
                        $variantValue = VariantValue::find($variantValueId);
                        if ($variantValue) {
                            $names[] = $variantValue->name;
                        }
                    }

                    $productName = implode(' ', $names);
                    $productSkuname = strtolower(implode('_', $names));

                    $html .= '<tr id="variant_combo_' . $comboString . '">';
                    $html .= '<td>
                                <span class="v_name">' . $productName . '</span>
                                <input type="hidden" class="v_input_name" name="variant_name[]" value="' . $productName . '">
                                <input type="hidden" class="v_input_variant_value_id" name="variant_id[]" value="' . $catVariant->id . '">
                            </td>';
                    $html .= '<td>
                                <span class="v_sku">SKU_' . $productSkuname . '</span>
                                <input type="hidden" class="v_input_sku" name="variant_sku[]" value="SKU_' . $productSkuname . '">
                            </td>';
                    $html .= '<td class="v_price">
                                <span class="s-price"></span>
                                <input type="hidden" class="v_input_price" name="variant_price[]" value="">
                            </td>';
                    $html .= '<td class="v_sale_price">
                                <span class="s-sale_price"></span>
                                <input type="hidden" class="v_input_sale_price" name="variant_sale_price[]" value="">
                            </td>';
                    $html .= '<td class="v_discount_type">
                                <span class="s-discount_type"></span>
                                <input type="hidden" class="v_input_discount_type" name="variant_discount_type[]" value="">
                            </td>';
                    $html .= '<td class="v_discount">
                                <span class="s-discount"></span>
                                <input type="hidden" class="v_input_discount" name="variant_discount[]" value="">
                            </td>';
                    $html .= '<td class="v_quantity">
                                <span class="s-quantity"></span>
                                <input type="hidden" class="v_input_quantity" name="variant_qty[]" value="">
                            </td>';
                    $html .= '<td>
                                <a href="javascript:void(0)" class="text-info" onclick="open_modal_edit(this)" data-id="' . $comboString . '"><i class="ri-edit-line"></i></a>';

                    if (count($split_variants[$key]) > 1) {
                        $html .= ' | <a href="javascript:void(0)" class="text-danger" onclick="open_modal(this)" data-id="' . $comboString . '"><i class="ri-delete-bin-5-line"></i></a>';
                    }

                    $html .= '</td>';
                }

                $html .= '</tbody></table></div></div></div>';
            }
        }

        return response()->json([
            'success' => true,
            'variantHtml' => $html,
            'totalProductVariant' => 0
        ]);
    }


    public function updateQty(Request $request)
    {
       
        $product = Product::find($request->product_id);

        if ($product) {
            $product->update([
                'qty' => $request->qty,
            ]);

            return redirect()
                ->route('admin-product-list')
                ->with('message', 'Quantity updated successfully.');
        }

        return redirect()
            ->route('admin-product-list')
            ->with('error', 'Product not found.');
    }

    public function generate_available_options($variants, $product_variants)
    {
        // Convert indexed input (0, 1index) to clean indexed arrays
        $cleaned_values = array_values($product_variants);

        // Recursive Cartesian product
        $combinations = [[]];

        foreach ($cleaned_values as $variant_values) {
            $temp = [];
            foreach ($combinations as $combination) {
                foreach ($variant_values as $value) {
                    $temp[] = array_merge($combination, [$value]);
                }
            }
            $combinations = $temp;
        }

        // Convert each combination to underscore string like 435_209
        $available_options = array_map(function ($combo) {
            return implode('_', $combo);
        }, $combinations);

        return $available_options;
    }

    public function productDetails(Request $request, $token)
    {

        try {
            $prdId = decrypt($token);

            $DB = Product::select('id', 'name', 'slug', 'buying_price', 'selling_price', 'category_id', 'sub_category_id', 'main_category_id', 'main_sub_category_id', 'main_child_category_id', 'in_stock', 'is_featured', 'is_active', 'draf', 'qty');
            $productLsit = $DB->with(['frontProductImage', 'firstProductImage', 'category', 'subCategory', 'mainCategory', 'mainSubCategory', 'mainChildCategory'])->where('id', $prdId)->first();

            return view('admin.products.view', compact('productLsit'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function exportProducts(Request $request)
    {
        return Excel::download(new ProductsExport($request->all()), 'products.xlsx');
    }

    private function generateAllCombinations(array $variantValues): array
    {
        $variantValues = array_values($variantValues); // Ensure proper indexing
        $combinations = [[]];
        foreach ($variantValues as $values) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($values as $value) {
                    $newCombinations[] = array_merge($combination, [$value]);
                }
            }
            $combinations = $newCombinations;
        }
        return array_map(function ($combo) {
            return implode('_', $combo);
        }, $combinations);
    }

    private function groupByPrimaryVariant($combinations, $primaryValues)
    {
        $grouped = [];
        foreach ($combinations as $combo) {
            $parts = explode('_', $combo);
            $primary = $parts[0];
            $grouped[$primary][] = $combo;
        }
        return $grouped;
    }

    public function getVarient($productId)
    {
        $productVariant = ProductVariant::with('variantValues')->where('product_id', $productId)->get();
        $variantIds = [];
        $variantValues = [];
        foreach ($productVariant as $variant) {
            $variantIds[] = $variant->variant_id;
            $variantValues[] = $variant->variantValues->pluck('variant_value_id')->toArray();
        }
        if (empty($variantValues)) {
            return '';
        }
        $primaryVariantId = $variantIds[0];
        $primaryValues = $variantValues[0];
        $allCombos = $this->generateAllCombinations($variantValues);
        $savedCombos = ProductVariantCombination::where('product_id', $productId)
            ->where('status', '1')
            ->pluck('combination_id')
            ->map(function ($combo) {
                $ids = json_decode($combo, true);

                return implode('_', $ids);
            })
            ->toArray();
        $deletedCombos = ProductVariantCombination::where('product_id', $productId)
            ->where('status', '0')
            ->pluck('combination_id')
            ->map(function ($combo) {
                $ids = json_decode($combo, true);
                return implode('_', $ids);
            })
            ->toArray();

        $existingCombos = array_intersect($allCombos, $savedCombos);
        $groupedCombinations = $this->groupByPrimaryVariant($existingCombos, $primaryValues);
        $groupedDeleted = $this->groupByPrimaryVariant($deletedCombos, $primaryValues);

        return response()->json([
            'html' => view("admin.products.variant_comb_modal", compact('primaryVariantId', 'groupedCombinations', 'groupedDeleted', 'productId'))->render()
        ]);
    }

    public function updateFeature(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'field' => 'required|in:in_stock,is_featured,is_active,best_seller,is_new_arrivals',
            'value' => 'required|boolean',
        ]);

        $productId = $request->input('product_id');
        $field = $request->input('field');
        $value = $request->input('value');

        // Update the product in the database
        if($field=='in_stock'){
            if($value==1){
                $value = 0;
            }else {
                $value = 1;
            }
        }
        Product::where('id', $productId)->update([$field => $value]);

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function updateFeatured(Request $request)
    {
        $productId = $request->input('product_id');
        $field = $request->input('field');
        $value = $request->input('value');

        Product::where('id', $productId)->update([$field => $value]);

        return response()->json(['success' => true]);
    }
}



// GD Resize Function outside the class
if (!function_exists('resizeImage')) {
    function resizeImage($filePath, $width, $height, $newFilePath)
    {
        // list($originalWidth, $originalHeight) = getimagesize($filePath);
        // $src = imagecreatefromstring(file_get_contents($filePath));

        // $dst = imagecreatetruecolor($width, $height);
        // imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        // imagejpeg($dst, $newFilePath);
        // imagedestroy($src);
        // imagedestroy($dst);

        $image = Image::read($filePath);
        $image->resize($width, $height);
        $image->save($newFilePath);
    }
}