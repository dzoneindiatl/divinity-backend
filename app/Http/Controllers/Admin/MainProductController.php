<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Variant;
use App\Models\{ChildCategory, CategoryVariant, CategoryAttribute, VariantValue};
use App\Models\Product;
use App\Models\ProductVariantCombination;
use App\Models\ProductGraphics;
use App\Models\Tag;
use App\Models\Attribute;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Country;
use App\Models\ProductVariantValue;
use App\Models\ProductVariant;
use App\Http\Requests\Product\{ProductTabRequest, ProductStep1, ProductStep2Request, ProductStep3Request};
use App\Services\ProductTabService;

use Session;
use Validator, Response, Redirect, Str, View, File;

class MainProductController extends Controller
{
    protected $productService;

    public function __construct(ProductTabService $productService)
    {
        $this->middleware('permission:view_product|create_product|edit_product|delete_product', [
            'only' => ['index', 'show', 'getVariantReleatedProduct', 'groupByPrimaryVariant', 'generateAvailableCombinations', 'generateAllCombinations', 'fileDelete']
        ]);
        $this->middleware('permission:create_product', [
            'only' => ['create', 'store', 'addNewProduct', 'getVariantReleatedProduct', 'groupByPrimaryVariant', 'generateAvailableCombinations', 'generateAllCombinations', 'fileDelete']
        ]);
        $this->middleware('permission:edit_product', [
            'only' => ['edit', 'update', 'status', 'saveStep1', 'saveStep2', 'saveStep3', 'saveStep4', 'previousStep2', 'previousStep3', 'previousStep', 'getVariantReleatedProduct', 'groupByPrimaryVariant', 'generateAvailableCombinations', 'generateAllCombinations', 'fileDelete']
        ]);
        $this->middleware('permission:delete_product', [
            'only' => ['destroy']
        ]);
        $this->productService = $productService;
    }



   public function addNewProduct(Request $request, $token = null)
{
    info('STEP 1', [
        'token' => $token,
    ]);

    $product = [];

    if ($token) {

        info('STEP 2 - Before decrypt');

        $id = decrypt($token);

        info('STEP 3 - After decrypt', [
            'id' => $id,
        ]);

        $product = Product::findOrFail($id);

        info('STEP 4 - After product', [
            'product_id' => $product->id,
        ]);
    }

    info('STEP 5 - Before categories');

    $categories = Category::where('is_deleted', 0)
        ->whereNull('parent_id')
        ->get();

    info('STEP 6 - After categories');

    return view(
        'admin.prodcuts.add-new-product',
        compact('categories', 'product')
    );
}

    /**
     * Methode :- POST
     * Function :- saveProduct
     * Description :- Save all information related to the product.
     *
     */

    public function saveStep1(ProductStep1 $request, ProductTabService $service)
    { 
        $product = $service->step1($request->all());
        return response()->json([
            'success' => true,
            'product_id' => $product->id,
            'varient' => $this->previousStep3($product->id),
            'message' => 'Step 1 saved successfully.',
        ]);
    }

    public function getProductCategoryVariant(Request $request){ 
        $product_type = $request->product_type; 
        $productId = $request->product_id; 
        $variantsData = CategoryVariant::with('variant:id,name')
            ->where('category_id', $request->main_category_id)
            ->get()
            ->pluck('variant')
            ->unique('id')
            ->values();
        
            $selectedVariants = ProductVariant::where('product_id', $productId)
            ->with('variantValues')
            ->get()
            ->map(function ($pv) {
                return [
                    'variant_id' => $pv->variant_id,
                    'variant_values' => $pv->variantValues->pluck('variant_value_id')->toArray()
                ];
            });
           
            $html = view('modals.products.create_variant_combined', [
                'variantsData' => $variantsData,
                'product_id' => $productId,
                'product_type' => $product_type,
                "selectedVariants" => $selectedVariants,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => [
                    'variantsData' => $variantsData,
                    'selectedVariants' => $selectedVariants
                ]
            ]);    
    }
    public function previousStep3($productId)
    {   
        $product = Product::findOrFail($productId);
        $attributesData = CategoryAttribute::with('attribute:id,name')
            ->where('category_id', $product->main_category_id)
            ->distinct()
            ->get()
            ->pluck('attribute')
            ->unique('id')
            ->values();
      
        $preselectedAttributes = ProductAttribute::where('product_id', $productId)->get();
        $categories = Category::with('children.children')->whereNull('parent_id')->where('is_deleted', 0)->get();
        $activeCategorie = Category::with('children.children')->where('is_deleted', 0)->whereNull('parent_id')->where('id', $product->main_category_id)->first(); 
        $variantReleatedProduct = $this->getVariantReleatedProduct($productId); 
        $tags = Tag::where('is_deleted', '!=', 1)->where('is_active', 1)->pluck('name', 'id')->toArray(); 
        $country = Country::where('is_active', 1)->get(); 
        return view('admin.prodcuts.advance_feature_combined', [
            'attributesData'            =>  $attributesData,
            "variantReleatedProduct"    =>  $variantReleatedProduct,
            "categories"                =>  $categories,
            "activeCategorie"           =>   $activeCategorie,
            "tags"                      =>  $tags,
            "countries"                 =>  $country,
            'product'                   =>  $product,
            'preselectedAttributes'     =>  $preselectedAttributes,

        ])->render();
    }

    

    public function saveStep3(ProductStep3Request $request, ProductTabService $service)
    {
        $service->step3($request->all());
        $getVarientId = activeVarientByProductId($request->product_id);
        $frontImg =  getActiveFrontImg($request->product_id,$getVarientId);
        $BackImg =  getActiveBackImg($request->product_id,$getVarientId);
        if( !empty($request->product_id) && !empty($getVarientId) && (empty($frontImg) || empty($BackImg))) {
            return response()->json([
                'success' => false,
                'message' => 'Please should be select Front/Back images compulsary.'
            ]);

        }
        $product = Product::findOrFail($request->product_id);
        $product->meta_title       = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->seo_content      = $request->seo_content;
        $product->is_active        = 1;
        $product->meta_keywords     = $request->meta_keywords;
        $product->save();
        $seoView = view('admin.prodcuts.seo_feature_combined', [
            'product' => $product
        ])->render();
        
        return response()->json([
            'success' => true,
            'message' => 'Product saved and published',
            'seoView'=>$seoView
        ]);
    }
    public function saveStep4(Request $request)
    {
        return redirect()->route('admin-product-list')->with('success', 'Product saved and published.');
    }

    public function updateFrontBackIcon(Request $request, ProductTabService $service){
        // dd($request->vid, $request->id, $request->type);
        $return = $service->updateFrontBackVariantImage($request->vid, $request->id, $request->type, $request->productid);
        return response()->json([
            'success' =>  $return
        ]);
    }

    public function getVariantReleatedProduct($product_id)
    {
        $productVariant = ProductVariant::with('variantValues')->where('product_id', $product_id)->get();

        $variantIds = [];
        $variantValues = [];

        foreach ($productVariant as $variant) {
            $variantIds[] = $variant->variant_id;
            $variantValues[] = $variant->variantValues->pluck('variant_value_id')->toArray();
        }

        if (empty($variantValues)) {
            return '';
        }

        $product = Product::where('id',$product_id)->first(); 
        $primaryVariantId = $variantIds[0];
        $primaryValues = $variantValues[0];

        $allCombos = $this->generateAllCombinations($variantValues);
        $savedCombos = ProductVariantCombination::where('product_id', $product_id)
            ->where('status', '1')
            ->pluck('combination_id')
            ->map(function ($combo) {
                $ids = json_decode($combo, true);

                return implode('_', $ids);
            })
            ->toArray();

        $deletedCombos = ProductVariantCombination::where('product_id', $product_id)
            ->where('status', '0')
            ->pluck('combination_id')

            ->map(function ($combo) {
                $ids = json_decode($combo, true);

                return implode('_', $ids);
            })
            ->toArray();
        $existingCombos = array_intersect($allCombos, $savedCombos);

        $groupedCombinations = $this->groupByPrimaryVariant($existingCombos, $primaryValues);
        $groupedDeleted = $this->groupByPrimaryVariant($deletedCombos, $primaryValues); // 👈 restore popup के लिए

        $primaryIds = array_keys($groupedCombinations);
        $mainVariantValueId = ProductVariantValue::where('product_id', $product_id)
            ->where('is_main', 1)
            ->value('variant_value_id');

        $primaryValueData = VariantValue::whereIn('id', $primaryIds)
            ->get()
            ->keyBy('id');

        $graphics = ProductGraphics::where('product_id', $product_id)
            ->whereIn('variant_id', $primaryIds)
            ->whereIn('graphic_type', ['image', 'video'])
            ->get()
            ->groupBy(function ($graphic) {
                return $graphic->variant_id . '_' . $graphic->graphic_type;
            });

        $outOfStockCombinations = ProductVariantCombination::where('product_id', $product_id)
            ->where('is_out_of_stock', 1)
            ->get();

        $imagePath = config('constant.PRODUCT_IMAGE_PATH');

        return view('admin.prodcuts.variant_combinations', compact(
            'primaryVariantId',
            'groupedCombinations',
            'groupedDeleted',
            'product_id',
            'mainVariantValueId',
            'primaryValueData',
            'graphics',
            'outOfStockCombinations',
            'imagePath',
            'product',
        ))->render();
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

    public function generateAvailableCombinations(array $variantValues, $product_id): array
    {
        $savedCombinations = ProductVariantCombination::where('product_id', $product_id)
            ->pluck('combination_id')
            ->where('status', '1')
            ->map(function ($combo) {
                $ids = json_decode($combo, true);

                return implode('_', $ids);
            })
            ->toArray();

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


        $result = [];

        foreach ($combinations as $combo) {
            $sorted = $combo;

            $key = implode('_', $sorted);

            if (in_array($key, $savedCombinations)) {
                $result[] = implode('_', $combo); // keep original order
            }
        }

        return $result;
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


    public function fileDelete(Request $request)
    {
        $image = ProductGraphics::find($request->id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Image not found.']);
        }

        // Delete from disk
        $imagePath = 'uploads/products/' . $image->graphic;
        if (file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }

        // Delete from DB
        $image->delete();

        return response()->json(['success' => true]);
    }


    public function previousStep(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        if ($request->step == "step1") {
            $categories = Category::with('children.children')->whereNull('parent_id')->where('is_deleted', 0)->get();
            $mainView = view('modals.products.create_product_combined', [
                'product' => $product,
                "categories" => $categories
            ])->render();

            return response()->json([
                'success' => true,
                'mainView' => $mainView,
                'message' => '',
                'step' => '1'
            ]);
        } elseif ($request->step == "step2") {
            return response()->json([
                'success' => true,
                'varient' => $this->previousStep2($request->product_id),
                'message' => '',
                'step' => '2'
            ]);
        } elseif ($request->step == "step3") {
            return response()->json([
                'success' => true,
                'mainView' => $this->previousStep3($request->product_id),
                'message' => '',
                'step' => '3'
            ]);
        } elseif ($request->step == "step4") {
            $seoView = view('admin.prodcuts.seo_feature_combined', [
                'product' => $product
            ])->render();
            return response()->json([
                'success' => true,
                'seoView' => $seoView,
                'message' => '',
                'step' => '4'
            ]);
        }
    }

    public function toggleStock(Request $request)
    {
        $variants = ProductVariantCombination::where('product_id', $request->product_id)
            ->whereJsonContains('combination_id',  (int) $request->variant_id)
            ->get();
        // dd($variants);
        
        foreach ($variants as $variant) {
            $variant->is_out_of_stock = $request->is_out_of_stock;
            if($request->is_out_of_stock==1){
                $variant->qty = 0;
            }
            $variant->save();
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function priceEdit(Request $request, $product_id)
    {
        $productVariant = ProductVariant::with('variantValues')->where('product_id', $product_id)->get();

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
        $savedCombos = ProductVariantCombination::where('product_id', $product_id)
            ->where('status', '1')
            ->pluck('combination_id')
            ->map(function ($combo) {
                $ids = json_decode($combo, true);

                return implode('_', $ids);
            })
            ->toArray();



        $deletedCombos = ProductVariantCombination::where('product_id', $product_id)
            ->where('status', '0')
            ->pluck('combination_id')

            ->map(function ($combo) {
                $ids = json_decode($combo, true);

                return implode('_', $ids);
            })
            ->toArray();
        $existingCombos = array_intersect($allCombos, $savedCombos);



        $groupedCombinations = $this->groupByPrimaryVariant($existingCombos, $primaryValues);
        $groupedDeleted = $this->groupByPrimaryVariant($deletedCombos, $primaryValues); // 👈 restore popup के लिए

        return view('admin.prodcuts.price_combinations', compact(
            'primaryVariantId',
            'groupedCombinations',
            'groupedDeleted',
            'product_id'
        ))->render();
    }

    public function updateVariantsPrices(Request $request)
    {
        // dd($request->all());
        foreach ($request->variants as $variant) {

            $comboIds = array_map('intval', explode('_', $variant['combo']));
           
            ProductVariantCombination::updateOrCreate(
                [
                    'product_id' => $request->product_id,
                    'combination_id' => json_encode($comboIds)
                ],
                [
                    'variant_id'     => $variant['variant_id'],
                    'sku'            => $variant['sku'],
                    'price'          => $variant['price'],
                    'discount_type'  => $variant['discount_type'],
                    'discount'       => $variant['discount'],
                    'selling_price'  => $variant['selling_price'],
                    'qty'            => abs($variant['qty']),
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Variants updated successfully'
        ]);
    }

    function updateProductOrder(Request $request)
    {
        $requestOrder    =    $request->input("requestData");

        if (!empty($requestOrder)) {
            foreach ($requestOrder as $product_order) {
                Product::where("id", $product_order["id"])->update(array("product_order" => $product_order["order"]));
            }
        }
        die;
    }
}
