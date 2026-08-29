<?php

namespace App\Models;

use App\Models\ChildCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductImage;
use App\Models\ProductVariantCombination;
use App\Models\ProductVariantCombinationImage;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\UserReview;
use App\Models\ProductGraphics;
use App\Models\ProductVariantValue;

use DB;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'front_image_url',
        'back_image_url',
        'front_image',
        'detaill_default_images',
    ];

    // public function product_main_images()
    // {
    //     return $this->hasMany(ProductGraphics::class, 'product_id', 'id')->where('graphic_type', 'image')->where('variant_id', NULL);
    // }

    public function product_main_images()
    {
        return $this->hasMany(ProductGraphics::class, 'product_id', 'id');
           // ->where('graphic_type', 'image')       
    }


    public function product_main_videos()
    {
        return $this->hasMany(ProductGraphics::class, 'product_id', 'id')->where('graphic_type', 'video')->where('variant_id', NULL);
    }

    public function getFrontImageAttribute()
    {
        if ($this->product_type == 1) {
            $prod_image = ProductImage::where(['product_id' => $this->id])->first();
        } else {
            $prod_image = ProductColorImage::where(['product_id' => $this->id])->orderBy('id', 'desc')->first();
        }

        if (!empty($prod_image)) {
            if ($this->product_type == 1)
                return $prod_image->image;
            else
                return Config('constant.PRODUCT_IMAGE_URL') . $prod_image->image;
        } else {
            return Config('constant.PRODUCT_IMAGE_URL') . 'noimage.png';
        }
    }

    public function getFrontImageUrlAttribute()
    {
        // Replace with your logic to generate the URL
        $prod_image = ProductColorImage::where(['product_id' => $this->id])->first();
        if (!empty($prod_image->image)) {
            return Config('constant.PRODUCT_IMAGE_URL') . $prod_image->image;
        }

        // Return a default image URL or null if no image is set
        return asset('storage/default.jpg');
    }

    public function getDetaillDefaultImagesAttribute()
    {
        // dd("Working");
        $mainVariant = ProductVariantValue::where([
            'product_id' => $this->id,
            'is_main' => 1
        ])->first(); // fixed typo

        if (!$mainVariant) {
            return [];
        }

        $variantImages = ProductGraphics::where([
            'product_id' => $this->id,
            'variant_id' => $mainVariant->veriant_value_id
        ])->get();

        return $variantImages->map(function ($image) {
            return [
                'id' => $image->id,
                'graphic' => config('constant.PRODUCT_IMAGE_URL') . $image->graphic,
            ];
        });
    }


    

    public function getBackImageUrlAttribute()
    {
        // Replace with your logic to generate the URL
        $prod_image = ProductColorImage::where(['product_id' => $this->id])->first();
        if (!empty($prod_image->image)) {
            return Config('constant.PRODUCT_IMAGE_URL') . $prod_image->image;
        }

        // Return a default image URL or null if no image is set
        return asset('storage/default.jpg');
    }
    // function getImageAttribute($value = "")
    // {
    //     if ($value != "" && File::exists(Config('constant.PRODUCT_IMAGE_ROOT_PATH') . $value)) {
    //         return Config('constant.PRODUCT_IMAGE_URL') . $prod_image->image;
    //     } else {
    //         return  Config('constant.IMAGE_URL') . "noimage.png";
    //     }
    // }
    public function getImageAttribute($value = "")
    {
        if ($value != "" && File::exists(config('constant.PRODUCT_IMAGE_ROOT_PATH') . $value)) {
            return config('constant.PRODUCT_IMAGE_URL') . $value;
        } else {
            return config('constant.IMAGE_URL') . "no-image.jpg";
        }
    }

    public function frontProductImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->where('is_front', 1);
    }
    
    public function firstProductImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id');
    }

    public function getDisplayImageAttribute()
    {
        return $this->frontProductImage ?? $this->firstProductImage;
    }

    

    public function addProductImages($images, $product_id)
    {
        if (!empty($images)) {
            ProductImage::where('product_id', $product_id)->delete();
            foreach ($images as $image) {
                $productImage = new ProductImage();
                $productImage->product_id = $product_id;
                $productImage->image = $image['path'];
                $productImage->is_front = $image['is_front'];
                $productImage->is_back = $image['is_back'];
                $productImage->save();
            }
        }
    }

    public function addProductAttribute($attributes, $product_id)
    {
        if (!empty($attributes)) {
            ProductAttribute::where(['product_id' => $product_id])->delete();
            foreach ($attributes as $attribute) {
                if (isset($attribute['value']['id']) && ($attribute['value']['id'] > 0)) {
                    $productAttribute = new ProductAttribute();
                    $productAttribute->product_id = $product_id;
                    $productAttribute->attribute_id = $attribute['id'];
                    // $productAttribute->save();

                    $attrValue = new ProductAttributeValue();
                    $attrValue->product_attribute_id = $productAttribute->id;
                    $attrValue->product_id = $product_id;
                    $attrValue->attribute_value_id = $attribute['value']['id'];
                }
                // $attrValue->save();
            }
        }
    }

    public function addProductVariant($variants, $product_id)
    {
        if (!empty($variants)) {
            $oldvariants = ProductVariant::where('product_id', $product_id)->get();

            foreach ($oldvariants as $old) {
                $oldvalues = ProductVariantValue::where('product_veriant_id', $old->id)->get();
                foreach ($oldvalues as $val) {
                    $val->delete();
                }
                $old->delete();
            }
            foreach ($variants as $variant) {
                $product_variant = new ProductVariant();
                $product_variant->product_id = $product_id;
                $product_variant->variant_id = $variant['id'];
                $product_variant->save();
                foreach ($variant['value'] as $key => $value) {
                    $product_variant_value = new ProductVariantValue();
                    $product_variant_value->product_veriant_id = $product_variant->id;
                    $product_variant_value->veriant_value_id = $value['id'];
                    $product_variant_value->price = intval($value['price']);
                    $product_variant_value->available = $value['available'];
                    $product_variant_value->name = $value['name'];
                    $product_variant_value->code = $value['code'];
                    $product_variant_value->save();
                }
            }
        }
    }

    public function getAllFeaturedProducts($categoryId = null, $subCategoryId = null, $childCategory = null)
    {

        // try {
        $DB = ProductVariantCombination::leftJoin('products', 'products.id', 'product_variant_combinations.product_id')->leftJoin('categories', 'products.category_id', '=', 'categories.id');

        if (!empty($categoryId)) {
            $DB->where('products.category_id', $categoryId);
        }
        if (!empty($subCategoryId)) {
            $DB->where('products.sub_category_id', $subCategoryId);
        }
        if (!empty($childCategory)) {
            $DB->where('products.child_category_id', $childCategory);
        }
        $limit = Config("Reading.records_per_page");
        // print_r($limit);die;
        // print_r($totalResults);die;
        $results = $DB->where('products.is_active', 1)->where('products.is_deleted', 0)->where('products.is_featured', 1)->select('product_variant_combinations.*', 'products.name', 'categories.name as category_name', 'products.is_featured', DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant1_value_id ) as variant_value1_name'), DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant2_value_id ) as variant_value2_name'))->groupBy('product_variant_combinations.id')->limit($limit)->get();
        if ($results->isNotEmpty()) {
            foreach ($results as $result) {
                $result->productImages = ProductVariantCombinationImage::where('product_variant_combination_images.product_variant_combination_id', $result->id)->leftJoin('product_images', 'product_images.id', 'product_variant_combination_images.product_image_id')->limit(2)->pluck('product_images.image')->toArray();
                $result->isProductAddedIntoCart = isProductAddedInCart($result->id) ? 1 : 0;
                $result->isProductAddedIntoWishlist = isProductAddedInWishlist($result->id) ? 1 : 0;
                if (!empty($result->productImages)) {
                    $tempProductImages = [];

                    foreach ($result->productImages as $productImageKey => $productImage) {
                        $productImage = (!empty($productImage)) ? Config('constant.PRODUCT_IMAGE_URL') . $productImage : Config('constant.IMAGE_URL') . "noimage.png";
                        $tempProductImages[$productImageKey] = $productImage;
                    }

                    $result->productImages = $tempProductImages;
                }
            }
        }
        return $results;



        // } catch (Exception $e) {
        //     Log::error($e);
        //     return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        // }
    }

    public function getAllBottomProducts($categoryId = null, $subCategoryId = null, $childCategory = null)
    {

        // try {
        $cat_data = Category::where('slug', "diamonds-engagement-ring")->first();

        $DB = ProductVariantCombination::leftJoin('products', 'products.id', 'product_variant_combinations.product_id')->leftJoin('categories', 'products.category_id', '=', 'categories.id');

        if (!empty($categoryId)) {
            $DB->where('products.category_id', $categoryId);
        }
        if (!empty($cat_data)) {
            $DB->where('products.sub_category_id', $cat_data->id);
        }
        if (!empty($childCategory)) {
            $DB->where('products.child_category_id', $childCategory);
        }
        $limit = Config("Reading.records_per_page");

        $results = $DB->where('products.is_active', 1)->where('products.is_deleted', 0)->select('product_variant_combinations.*', 'products.name', 'categories.name as category_name', 'products.is_featured', DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant1_value_id ) as variant_value1_name'), DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant2_value_id ) as variant_value2_name'))->groupBy('product_variant_combinations.id')->limit($limit)->get();
        if ($results->isNotEmpty()) {
            foreach ($results as $result) {
                $result->productImages = ProductVariantCombinationImage::where('product_variant_combination_images.product_variant_combination_id', $result->id)->leftJoin('product_images', 'product_images.id', 'product_variant_combination_images.product_image_id')->limit(2)->pluck('product_images.image')->toArray();
                $result->isProductAddedIntoCart = isProductAddedInCart($result->id) ? 1 : 0;
                $result->isProductAddedIntoWishlist = isProductAddedInWishlist($result->id) ? 1 : 0;
                if (!empty($result->productImages)) {
                    $tempProductImages = [];

                    foreach ($result->productImages as $productImageKey => $productImage) {
                        $productImage = (!empty($productImage)) ? Config('constant.PRODUCT_IMAGE_URL') . $productImage : Config('constant.IMAGE_URL') . "noimage.png";
                        $tempProductImages[$productImageKey] = $productImage;
                    }

                    $result->productImages = $tempProductImages;
                }
            }
        }
        return $results;



        // } catch (Exception $e) {
        //     Log::error($e);
        //     return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        // }
    }


    public function getAllCategoryProductsOnDetailPage($categoryId = null, $subCategoryId = null, $childCategory = null)
    {
        $DB = ProductVariantCombination::leftJoin('products', 'products.id', 'product_variant_combinations.product_id')->leftJoin('categories', 'products.category_id', '=', 'categories.id');

        if (!empty($categoryId)) {
            $DB->where('products.category_id', $categoryId);
        }
        if (!empty($subCategoryId)) {
            $DB->where('products.sub_category_id', $subCategoryId);
        }
        if (!empty($childCategory)) {
            $DB->where('products.child_category_id', $childCategory);
        }
        $limit = 7;
        $results = $DB->where('products.is_active', 1)->where('products.is_deleted', 0)->select('product_variant_combinations.*', 'products.name', 'products.category_id', 'products.sub_category_id', 'products.child_category_id', 'categories.name as category_name', 'products.is_featured', DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant1_value_id ) as variant_value1_name'), DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant2_value_id ) as variant_value2_name'))->groupBy('product_variant_combinations.id')->limit($limit)->get();
        if ($results->isNotEmpty()) {
            foreach ($results as $result) {
                $result->productImages = ProductVariantCombinationImage::where('product_variant_combination_images.product_variant_combination_id', $result->id)->leftJoin('product_images', 'product_images.id', 'product_variant_combination_images.product_image_id')->limit(2)->pluck('product_images.image')->toArray();
                $result->isProductAddedIntoCart = isProductAddedInCart($result->id) ? 1 : 0;
                $result->isProductAddedIntoWishlist = isProductAddedInWishlist($result->id) ? 1 : 0;
                if (!empty($result->productImages)) {
                    $tempProductImages = [];

                    foreach ($result->productImages as $productImageKey => $productImage) {
                        $productImage = (!empty($productImage)) ? Config('constant.PRODUCT_IMAGE_URL') . $productImage : Config('constant.IMAGE_URL') . "noimage.png";
                        $tempProductImages[$productImageKey] = $productImage;
                    }

                    $result->productImages = $tempProductImages;
                }
            }
        }
        return $results;
    }


    public function getAllHomeSubCatProducts($subCategoryId)
    {

        // try {

        $DB = ProductVariantCombination::leftJoin('products', 'products.id', 'product_variant_combinations.product_id')->leftJoin('categories', 'products.category_id', '=', 'categories.id');

        if (!empty($subCategoryId)) {
            $DB->where('products.sub_category_id', $subCategoryId);
        }

        $limit = Config("Reading.records_per_page");
        // print_r($limit);die;
        // print_r($totalResults);die;
        $results = $DB->select('product_variant_combinations.*', 'products.name', 'categories.name as category_name', 'products.is_featured')->groupBy('product_variant_combinations.id')->limit($limit)->get();
        if ($results->isNotEmpty()) {
            foreach ($results as $result) {
                $result->productImages = ProductVariantCombinationImage::where('product_variant_combination_images.product_variant_combination_id', $result->id)->leftJoin('product_images', 'product_images.id', 'product_variant_combination_images.product_image_id')->limit(2)->pluck('product_images.image')->toArray();
                $result->isProductAddedIntoCart = isProductAddedInCart($result->id) ? 1 : 0;
                if (!empty($result->productImages)) {
                    $tempProductImages = [];

                    foreach ($result->productImages as $productImageKey => $productImage) {
                        $productImage = (!empty($productImage)) ? Config('constant.PRODUCT_IMAGE_URL') . $productImage : Config('constant.IMAGE_URL') . "noimage.png";
                        $tempProductImages[$productImageKey] = $productImage;
                    }

                    $result->productImages = $tempProductImages;
                }
            }
        }
        return $results;



        // } catch (Exception $e) {
        //     Log::error($e);
        //     return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        // }
    }

    // public function getRemainingQuantity($product_id,$value_array)
    // {
    //     $product = Product::where('id',$product_id)->with('productVariants')->first();
    //     foreach($product->productVariants as $product_variant){
    //         dd($product_variant);
    //     }
    // }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function descriptions()
    {
        return $this->hasMany(ProductDescription::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id")->with('parentcategory');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
    
    public function mainCategory()
    {
        return $this->belongsTo(Category::class, "main_category_id");
    }
    
     public function mainSubCategory()
    {
        return $this->belongsTo(Category::class, 'main_sub_category_id');
    }
    
    public function mainChildCategory()
    {
        return $this->belongsTo(Category::class, 'main_child_category_id');
    }

    public function mailSubCategory()
    {
        return $this->belongsTo(Category::class, 'main_sub_category_id');
    }

    public function childCategory()
    {
        return $this->belongsTo(Category::class, 'child_category_id');
    }

    // public function mainChildCategory()
    // {
    //     return $this->belongsTo(Category::class, 'main_child_category_id');
    // }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function ProductVariantCombination()
    {
        return $this->hasMany(ProductVariantCombination::class, 'product_id');
    }

    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function reviews()
    {
        return $this->hasMany(UserReview::class, 'product_id');
    }
}
