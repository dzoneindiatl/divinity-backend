<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\{ProductVariantValue,VariantValue};
use App\Models\ProductVariantCombination;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductGraphics;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductTabService
{


    public function step1(array $data): Product
    {

       
        $product = isset($data['product_id']) && $data['product_id']
            ? Product::findOrFail($data['product_id'])
            : new Product();

        $product->product_type         = $data['product_type'];
        $product->main_category_id     = $data['main_category_id'];
        $product->main_sub_category_id = $data['main_sub_category_id'] ?? null;
        $product->main_child_category_id  = $data['main_child_cate_id'] ?? null;

        $product->save();

        return $product;
    }

    public function step2(array $data)
    {
        $productId = $data['product_id'];

        \DB::transaction(function () use ($data, $productId) {

            $incomingVariants = $data['variant'] ?? [];
            $incomingVariantValues = $data['variant_values'] ?? [];

            $existingVariants = ProductVariant::where('product_id', $productId)->get()->keyBy('variant_id');

            $existingValues = ProductVariantValue::where('product_id', $productId)->get();

            $incomingVariantIds = collect($incomingVariants)->unique()->values();

            $incomingValueMap = collect();

            foreach ($incomingVariants as $i => $variantId) {
                $variantModel = $existingVariants[$variantId] ?? ProductVariant::create([
                    'product_id' => $productId,
                    'variant_id' => $variantId
                ]);

                $incomingValueIds = collect($data['variant_values'][$i])->map(fn($v) => (int) $v)->unique()->values();

                $incomingValueMap->push([
                    'variant_id' => $variantId,
                    'product_variant_id' => $variantModel->id,
                    'values' => $incomingValueIds
                ]);

                foreach ($incomingValueIds as $valueId) {
                    $exists = $existingValues->firstWhere(function ($val) use ($variantModel, $valueId) {
                        return $val->product_variant_id == $variantModel->id && $val->variant_value_id == $valueId;
                    });

                    if (!$exists) {
                        ProductVariantValue::create([
                            'product_variant_id' => $variantModel->id,
                            'variant_value_id'   => $valueId,
                            'product_id'         => $productId
                        ]);
                    }
                }
            }

            // Now delete ProductVariantValues not in new input
            $validProductVariantIds = $incomingValueMap->pluck('product_variant_id')->toArray();
            $validValueCombos = $incomingValueMap->flatMap(function ($row) {
                return $row['values']->map(fn($v) => $row['product_variant_id'] . '|' . $v);
            })->toArray();

            foreach ($existingValues as $value) {
                $key = $value->product_variant_id . '|' . $value->variant_value_id;
                if (!in_array($key, $validValueCombos)) {
                    $value->delete();
                }
            }

            // Delete variants not in new input
            foreach ($existingVariants as $variantId => $variant) {
                if (!$incomingVariantIds->contains($variantId)) {
                    $variant->delete();
                }
            }

            // Generate combinations (same as before)
            $variantValueMap = $data['variant_values'] ?? [];

            $uniqueGroups = collect($variantValueMap)->unique(function ($item) {
                return implode('_', $item);
            })->values()->toArray();

            $combinations = $this->cartesianProduct($uniqueGroups);

            foreach ($combinations as $combo) {
               
              $valueIds = array_map('intval', $combo);
              
                $jsonCombo = json_encode($valueIds);   

                $existing = ProductVariantCombination::where('product_id', $productId)
                ->where('combination_id', $jsonCombo) 
                ->first();

                if ($existing) continue;
                 
                $values = collect($valueIds)
                    ->map(function ($id) {
                        return \App\Models\VariantValue::find($id);
                    })
                    ->filter();

                    $name = $values->pluck('name')->implode(' ');
                    $sku  = strtolower($values->pluck('name')->implode('_'));

                    ProductVariantCombination::create([
                        'product_id'     => $productId,
                        'sku'            => $sku,
                        'combination_id' => json_encode($valueIds),
                        'name'           => $name,
                        'selling_price'  => 0.0,
                        'price'          => 0.0,
                        'qty'            => 0,
                    ]);
            }

            $newJsonCombos = collect($combinations)->map(fn($combo) => json_encode(array_map('intval', $combo)))->toArray();

            ProductVariantCombination::where('product_id', $productId)
                ->whereNotIn('combination_id', $newJsonCombos)
                ->delete();
            });
    }

    private function cartesianProduct($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public function step3(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $productTags = is_array($data['product_tags'] ?? null)
                ? implode(',', $data['product_tags']) : ($data['product_tags'] ?? '');

            $product = Product::findOrFail($data['product_id']);
            $product->update([
                'parent_id' => 0,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . uniqid(),
                'sku' => $data['sku'],
                "country_origin"=>$data['country_origin'],
              
                // Related Categories (Multi)
                'category_id' => !empty($data['category_id']) ? json_encode($data['category_id']) : '',
                'sub_category_id' => !empty($data['sub_category_id']) ? json_encode($data['sub_category_id']) : '',
                'child_category_id' => !empty($data['child_category_id']) ? json_encode($data['child_category_id']) : '',

                // Related Products
                'related_product_categores_id' => $data['categorys_id'] ?? '',
                'related_product_subcategory_id' => $data['subcategory_id'] ?? '',
                'related_products' => (isset($data['Product_id']) && is_array($data['Product_id']))
                    ? implode(',', $data['Product_id']) : '',

                // Descriptions
                'description' => $data['description'] ?? '',
                'specification' => $data['specification'] ?? '',
                'short_description' => $data['short_description'] ?? '',
                'long_description' => $data['long_description'] ?? '',
                'product_details' => $data['product_details'] ?? '',
                'others' => $data['others'] ?? '',

                // Pricing
                'buying_price' => $data['buying_price'] ?? '',
                'discount' => $data['discount'] ?? '',
                'discount_type' => $data['discount_type'] ?? '',
                'selling_price' => $data['selling_price'] ?? '',
                'qty' => $data['qty'] ?? '',

                // Weight
                'weight' => $data['weight'] ?? '',
                'weight_type' => $data['weight_type'] ?? '',

                'product_tags' => $productTags,

                // Status & Flags
                'draf' => 0,
                'is_active' => $data['is_active'] ?? 1,
                'is_new' => isset($data['is_new']) ? 1 : 0,
                'is_new_arrivals' => isset($data['is_new_arrivals']) ? 1 : 0,
                'is_featured' => isset($data['is_featured']) ? 1 : 0,
                'trending' => isset($data['trending']) ? 1 : 0,
                'best_selling' => isset($data['best_selling']) ? 1 : 0,
                'best_seller' => isset($data['best_seller']) ? 1 : 0,

                // Miscellaneous
                'hsn' => $data['hsn'] ?? '',
                'bar_code' => $data['bar_code'] ?? '',
                'wash_care' => $data['wash_care'] ?? '',
                'max_selling_units' => $data['max_selling_units'] ?? ''
            ]);

            $this->handleVariants($product, $data);
            $this->handleAttributes($product, $data);
            $this->handleGraphics($product, $data);

            return $product;
        });
    }

    protected function handleVariants(Product $product, array $data)
    {
       
        ProductVariantValue::where('product_id', $product->id)->update(['is_main' => 0]);

        // Set selected main variant
        if (!empty($data['main_variant'])) {
            ProductVariantValue::where('product_id', $product->id)
                ->where('variant_value_id', $data['main_variant'])
                ->update(['is_main' => 1]);
        }

        $submittedCombinations = [];

        $totalCombinations = count($data['variant_name'] ?? []);
        for ($i = 0; $i < $totalCombinations; $i++) {
            $sku         = $data['variant_sku'][$i] ?? '';
            $combo       = $data['combo'][$i] ?? '';
            $price       = $data['variant_price'][$i] ?? 0;
            $salePrice   = $data['variant_sale_price'][$i] ?? $price;
            $qty         = $data['variant_qty'][$i] ?? 0;
            $discount    = $data['variant_discount'][$i] ?? null;
            $discountType = $data['variant_discount_type'][$i] ?? null;

            $valueIds = array_map('intval', explode('_', $combo));
            $encodedCombo = json_encode($valueIds);
            $submittedCombinations[] = $encodedCombo;

    
            $existing = ProductVariantCombination::where('product_id', $product->id)
                ->where('combination_id', $encodedCombo)
                ->first();

            if ($existing) {
               
                $existing->update([
                    'sku'           => $sku,
                    'selling_price' => $salePrice,
                    'price'         => $price,
                    'qty'           => $qty,
                    'discount'      => $discount,
                    'discount_type' => $discountType,
                    'status'        => "1"
                ]);
            } else {
                // Create new
                ProductVariantCombination::create([
                    'product_id'     => $product->id,
                    'sku'            => $sku,
                    'combination_id' => $encodedCombo,
                    'selling_price'  => $salePrice,
                    'price'          => $price,
                    'qty'            => $qty,
                    'discount'       => $discount,
                    'discount_type'  => $discountType,
                    'status'         => "1"
                ]);
            }
        }

        // Deactivate old combinations not present in current form
        ProductVariantCombination::where('product_id', $product->id)
            ->whereNotIn('combination_id', $submittedCombinations)
            ->update(['status' => "0"]);
    }



    protected function handleAttributes(Product $product, array $data)
    {
        ProductAttribute::where('product_id', $product->id)->delete();
        $attributeIds = $data['attribute_ids'] ?? [];
        $attributeValueIds = $data['attribute_value_ids'] ?? [];

        foreach ($attributeIds as $attributeId) {
            $selectedValueId = $attributeValueIds[$attributeId][0] ?? null;
            if ($selectedValueId) {
                ProductAttribute::create([
                    'product_id'         => $product->id,
                    'attribute_id'       => $attributeId,
                    'attribute_value_id' => $selectedValueId,
                ]);
            }
        }
    }

    protected function handleGraphics(Product $product, array $data)
{
    $imagePath = config('constant.PRODUCT_IMAGE_ROOT_PATH');

    foreach ($data['variant_images'] ?? [] as $primaryId => $files) {
        foreach ($files as $index => $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $name = uniqid("variant_{$primaryId}_") . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0777, true);
            }

            $file->move($imagePath, $name);

            ProductGraphics::create([
                'product_id'   => $product->id,
                'variant_id'   => $primaryId,
                'product_type' => 'variant_group',
                'graphic_type' => "image",
                'graphic'      => $name,
                'status'       => 1,
                'is_front'     => isset($data['front_image'][$primaryId]) && $data['front_image'][$primaryId] == "{$primaryId}-{$index}" ? 1 : 0,
                'is_back'      => isset($data['back_image'][$primaryId]) && $data['back_image'][$primaryId] == "{$primaryId}-{$index}" ? 1 : 0,
            ]);
        }
    }

        foreach ($data['variant_video'] ?? [] as $primaryId => $file) {
            if ($file->isValid()) {
                $name = uniqid('variant_video_' . $primaryId . '_') . '.' . $file->getClientOriginalExtension();
                $file->move($imagePath, $name);

                ProductGraphics::create([
                    'product_id' => $product->id,
                    'variant_id' => $primaryId,
                    'product_type' => 'variant_group',
                    'graphic_type' => 'video',
                    'graphic' => $name,
                    'status' => 1,
                    'is_front' => 0,
                    'is_back' => 0,
                ]);
            }
        }
    }
}
