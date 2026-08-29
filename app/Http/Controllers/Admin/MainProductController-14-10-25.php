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
        $product = [];
        if ($token) {
            $id = decrypt($token);
            $product = Product::FindOrFail($id);
        }
        $categories = Category::where('is_deleted', 0)->whereNull('parent_id')->get();


        return view('admin.prodcuts.add-new-product', compact('categories', "product",));
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
            'varient' => $this->previousStep2($product->id),
            'message' => 'Step 1 saved successfully.',
        ]);
    }


    public function previousStep2($productId)
    {
        $product = Product::findOrFail($productId);
        $productId = $product->id;
        $variants = CategoryVariant::with('variant:id,name')
            ->where('category_id', $product->main_category_id)
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



        return $variantView = view('modals.products.create_variant_combined', [
            'variantsData' => $variants,
            'product_id' => $productId,
            "selectedVariants" => $selectedVariants
        ])->render();
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

        $preselectedAttributes = ProductAttribute::where('product_id', $productId)
            ->get();
        return $mainView = view('admin.prodcuts.advance_feature_combined', [
            'attributesData'            =>  $attributesData,
            "variantReleatedProduct"    =>  $this->getVariantReleatedProduct($productId),
            "categories"                =>  Category::with('children.children')->whereNull('parent_id')->where('is_deleted', 0)->get(),
            "activeCategorie"          => Category::with('children.children')->where('is_deleted', 0)->whereNull('parent_id')->where('id', $product->main_category_id)->first(),
            "tags"                      =>  Tag::where('is_deleted', '!=', 1)->where('is_active', 1)->pluck('name', 'id')->toArray(),
            "countries"                 =>  Country::where('is_active', 1)->get(),
            'product'                   =>  $product,
            'preselectedAttributes'     =>  $preselectedAttributes,
        ])->render();
    }

    public function saveStep2(Request $request, ProductTabService $service)
    {

        $service->step2($request->all());
        return response()->json([
            'success' => true,
            'mainView' => $this->previousStep3($request->product_id),
            'message' => 'Step 2 (Variants) saved successfully.'
        ]);
    }

    public function saveStep3(ProductStep3Request $request, ProductTabService $service)
    {
        $service->step3($request->all());
        $product = Product::findOrFail($request->product_id);
        $seoView = view('admin.prodcuts.seo_feature_combined', [
            'product' => $product
        ])->render();

        return response()->json([
            'success' => true,
            'seoView' => $seoView,
            'message' => 'Step 3 saved successfully.'
        ]);
    }


    public function saveStep4(Request $request)
    {
        $request->validate([
            'meta_title'       => 'nullable|string',
            'meta_description' => 'nullable|string',
            'seo_content'      => 'nullable|string',

        ]);

        $product = Product::findOrFail($request->product_id);
        $product->meta_title       = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->seo_content      = $request->seo_content;
        $product->is_active        = 1;
        $product->meta_keywords     = $request->meta_keywords;
        $product->save();

        return redirect()->route('admin-product-list')->with('success', 'Product saved and published.');
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

        return view('admin.prodcuts.variant_combinations', compact(
            'primaryVariantId',
            'groupedCombinations',
            'groupedDeleted',
            'product_id'
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
        if ($request->step == "step1") {
            $product = Product::findOrFail($request->product_id);
            $categories = Category::with('children.children')->whereNull('parent_id')->where('is_deleted', 0)->get();
            $mainView = view('modals.products.create_product_combined', [
                'product' => $product,
                "categories" => $categories
            ])->render();

            return response()->json([
                'success' => true,
                'mainView' => $mainView,
                'message' => ''
            ]);
        } elseif ($request->step == "step2") {
            return response()->json([
                'success' => true,
                'mainView' => $this->previousStep2($request->product_id),
                'message' => ''
            ]);
        } elseif ($request->step == "step3") {
            return response()->json([
                'success' => true,
                'mainView' => $this->previousStep3($request->product_id),
                'message' => ''
            ]);
        }
    }
}
