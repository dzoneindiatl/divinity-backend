<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Variant;
use App\Models\VariantValue;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductGraphics;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use App\Models\ProductVariantCombination;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        if (empty($row['name']) || empty($row['sku'])) {
            return null;
        }

        return DB::transaction(function () use ($row) {
            $category_id = Category::where('name', $row['category'])->value('id');
            $sub_category_id = Category::where('name', $row['sub_category'])->value('id');
            $child_category_id = Category::where('name', $row['child_category'])->value('id');

            $product_type = strtoupper($row['product_type'] ?? 'Simple') === 'Simple' ? 1 : 2;
            $is_new_arrivals = strtoupper($row['is_new_arrivals'] ?? 'N') === 'Y' ? 1 : 0;
            $best_seller = strtoupper($row['best_seller'] ?? 'N') === 'Y' ? 1 : 0;

            // $attributes = array_map('trim', explode(',', $row['attributes']));
            // $attribute_values = array_map('trim', explode(',', $row['attribute_value']));
            $product = Product::updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'product_type' => $product_type,
                    'name' => $row['name'],
                    'main_category_id' => $category_id,
                    'main_sub_category_id' => $sub_category_id,
                    'main_child_category_id' => $child_category_id,
                    'buying_price' => $row['buying_price'] ?? 0,
                    'selling_price' => $row['selling_price'] ?? 0,
                    'discount' => $row['discount'] ?? 0,
                    'discount_type' => $row['discount_type'] ?? 'flat',
                    'qty' => $row['qty'] ?? 0,
                    'description' => $row['description'],
                    'specification' => $row['specification'],
                    'product_details' => $row['product_details'],
                    'short_description' => $row['short_description'],
                    'weight' => $row['weight'],
                    'weight_type' => $row['weight_type'],
                    'hsn' => $row['hsn'],
                    'wash_care' => $row['wash_care'],
                    'others' => $row['others'],
                    'in_stock' => 0,
                    'is_new_arrivals' => $is_new_arrivals,
                    'best_seller' => $best_seller,
                    'max_selling_units' => $row['max_selling_units'],
                    'min_selling_units' => $row['min_selling_units'],
                ]
            );

            // Attribute
            if ($row['attributes_1']) {

                $attribute_value_detail = AttributeValue::where('name', $row['attribute_value_1'])->first();
                if($attribute_value_detail){
                    ProductAttribute::firstOrCreate([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute_value_detail->attribute_id,
                        'attribute_value_id' => $attribute_value_detail->id,
                    ]);
                }
                
            }

            if ($row['attributes_2']) {

                $attribute_value_detail = AttributeValue::where('name', $row['attribute_value_2'])->first();
                if($attribute_value_detail){
                    ProductAttribute::firstOrCreate([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute_value_detail->attribute_id,
                        'attribute_value_id' => $attribute_value_detail->id,
                    ]);
                }
                
            }

            if ($row['attributes_3']) {

                $attribute_value_detail = AttributeValue::where('name', $row['attribute_value_3'])->first();
                if($attribute_value_detail){
                    ProductAttribute::firstOrCreate([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute_value_detail->attribute_id,
                        'attribute_value_id' => $attribute_value_detail->id,
                    ]);
                }
                
            }
            if ($row['attributes_4']) {

                $attribute_value_detail = AttributeValue::where('name', $row['attribute_value_4'])->first();
                if($attribute_value_detail){
                    ProductAttribute::firstOrCreate([
                        'product_id' => $product->id,
                        'attribute_id' => $attribute_value_detail->attribute_id,
                        'attribute_value_id' => $attribute_value_detail->id,
                    ]);
                }
                
            }

           // Variants 
            $sizes = array_map('trim', explode(',', $row['size']));
            $skus = array_map('trim', explode(',', $row['v_sku']));
            $prices = array_map('trim', explode(',', $row['v_price']));
            $sale_prices = array_map('trim', explode(',', $row['v_sale_price']));
            $discounts = array_map('trim', explode(',', $row['v_discount']));
            $discount_types = array_map('trim', explode(',', $row['v_discount_type']));
            $qtys = array_map('trim', explode(',', $row['v_qty']));
            // Color Variant
            $colorValueId = VariantValue::where('name', trim($row['color']))->value('id');

            $colorVariant = ProductVariant::firstOrCreate([
                'product_id' => $product->id,
                'variant_id' => 1,
            ]);

            ProductVariantValue::firstOrCreate([
                'product_id' => $product->id,
                'product_variant_id' => $colorVariant->id,
                'variant_value_id' => $colorValueId,
            ], ['is_main' => true]);

            // Size Variant
            $sizeVariant = ProductVariant::firstOrCreate([
                'product_id' => $product->id,
                'variant_id' => 2,
            ]);

            foreach ($sizes as $i => $size) {

                $sizeValueId = VariantValue::where('name', $size)->value('id');

                ProductVariantValue::firstOrCreate([
                    'product_id' => $product->id,
                    'product_variant_id' => $sizeVariant->id,
                    'variant_value_id' => $sizeValueId,
                ], ['is_main' => $i === 0]);

                ProductVariantCombination::firstOrCreate([
                    'product_id' => $product->id,
                    'sku' => $skus[$i],
                ], [
                    'combination_id' => json_encode([$colorValueId, $sizeValueId]),
                    'price' => $prices[$i],
                    'selling_price' => $sale_prices[$i],
                    'discount' => $discounts[$i],
                    'discount_type' => $discount_types[$i],
                    'qty' => $qtys[$i] ?? 0,
                ]);
            }

            // Save Product Graphics
            $imagePath = config('constant.PRODUCT_IMAGE_ROOT_PATH');
            $graphics = [
                'front' => $row['front_image'] ?? null,
                'back' => $row['back_image'] ?? null,
                'variant' => $row['variant_image'] ?? null,
                'image1' => $row['image1'] ?? null,
                'image2' => $row['image2'] ?? null,
                'image3' => $row['image3'] ?? null,
                'image4' => $row['image4'] ?? null,
                'image5' => $row['image5'] ?? null,
            ];

            foreach ($graphics as $type => $url) {

                if (empty($url)) {
                    continue;
                }

                $fileName = $this->storeImageFromGoogleDrive($url, $imagePath, $colorValueId);

                if (!$fileName) {
                    continue;
                }

                ProductGraphics::create([
                    'product_id'       => $product->id,
                    'variant_id'       => $colorValueId,
                    'product_type'     => 'variant_group',
                    'graphic'          => $fileName,
                    'graphic_type'     => 'image',
                    'status'           => 1,
                    'is_front'         => $type === 'front' ? 1 : 0,
                    'is_back'          => $type === 'back' ? 1 : 0,
                    'is_variant_icon'  => $type === 'variant' ? 1 : 0,
                ]);
            }

            return $product;
        });
    }

    private function storeImageFromGoogleDrive(string $url, string $folderPath, $colorValueId)
    {
        if (empty($url)) {
            return null;
        }

        // Extract Google Drive file ID
        if (preg_match('/\/d\/([^\/]+)\//', $url, $matches)) {
            $fileId = $matches[1];
        } elseif (preg_match('/id=([^&]+)/', $url, $matches)) {
            $fileId = $matches[1];
        } else {
            return null; // Invalid Drive URL
        }

        $downloadUrl = "https://drive.google.com/uc?export=download&id={$fileId}";

        $response = Http::withOptions([
            'verify' => false, // 👈 THIS fixes cURL error 60
        ])
        ->timeout(30)
        ->get($downloadUrl);

        if (!$response->successful()) {
            return null;
        }

        // Detect extension
        $extension = 'jpg';
        $contentType = $response->header('Content-Type');

        if (str_contains($contentType, 'png')) {
            $extension = 'png';
        } elseif (str_contains($contentType, 'webp')) {
            $extension = 'webp';
        }

        // Ensure directory exists
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $fileName = 'variant_' . $colorValueId . '_' . uniqid() . '.' . $extension;
        $fullPath = rtrim($folderPath, '/') . '/' . $fileName;

        // Save file
        file_put_contents($fullPath, $response->body());

        return $fileName;
    }

}
