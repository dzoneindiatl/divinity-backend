<?php 
namespace App\Models;
use Illuminate\Support\Facades\DB;
use App\Models\ProductGraphics;
use Illuminate\Database\Eloquent\Model;
/**
 * Review Model
 */
class ProductVariantCombination extends Model {

	
/**
 * The database table used by the model.
 *
 * @var string
 */
	protected $table = 'product_variant_combinations';

    protected $fillable = [
        'product_id',
        'combination_id',
        'sku',
        'selling_price',
        'discount',
        'discount_type',
        'price',
        'qty',
        "status",
        "is_out_of_stock"
    ];
    public function variant_images(){
        return $this->hasMany(ProductGraphics::class, 'variant_id', 'primary_variant_value_id')->where('status', 1)->where('graphic_type', 'image');
    }

    public function primary_variant_value()
    {
        return $this->belongsTo(ProductVariantValue::class, 'primary_variant_value_id', 'veriant_value_id')->select('veriant_value_id', 'is_main'); // Optional - for scoped correctness
    }
   

    public function variant_videos(){
        return $this->hasMany(ProductGraphics::class, 'variant_id', 'primary_variant_value_id')->where('status', 1)->where('graphic_type', 'video');
    }

    public function variant_main_image(){
        return $this->hasOne(ProductGraphics::class, 'variant_id', 'id')->where('status', 1)->where('graphic_type', 'image')->orderBy('id', 'asc');
    }

	function fetchProductDetailsByProductId($productId){
		$returnData = [];
		$productDetails = ProductVariantCombination::where('product_variant_combinations.id', $productId ?? 0)->leftJoin('products', 'products.id', 'product_variant_combinations.product_id')->select('product_variant_combinations.*', 'products.name','products.category_id','products.sub_category_id','products.child_category_id',DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant1_value_id ) as variant_value1_name'),DB::raw('(SELECT name from variant_values WHERE id = product_variant_combinations.variant2_value_id ) as variant_value2_name'))->first();
        $returnData['product_name'] = $productDetails->name ?? '';
        $returnData['category_id'] = $productDetails->category_id ?? '';
        $returnData['sub_category_id'] = $productDetails->sub_category_id ?? '';
        $returnData['child_category_id'] = $productDetails->child_category_id ?? '';
        $returnData['variant_value1_name'] = $productDetails->variant_value1_name ?? '';
        $returnData['variant_value2_name'] = $productDetails->variant_value2_name ?? '';
        $returnData['product_price'] = getDropPrices($productDetails->id,['category_id' => $productDetails->category_id, 'sub_category_id' => $productDetails->sub_category_id, 'child_category_id' => $productDetails->child_category_id,'selling_price' => $productDetails->selling_price,'product_id' => $productDetails->product_id],'selling') ;
        $productImage = ProductVariantCombinationImage::where('product_variant_combination_images.product_variant_combination_id', $productDetails->id)->leftJoin('product_images', 'product_images.id', 'product_variant_combination_images.product_image_id')->value('product_images.image');
        $returnData['product_image'] = (!empty($productImage)) ? Config('constant.PRODUCT_IMAGE_URL') . $productImage : Config('constant.IMAGE_URL') . "noimage.png";
		return $returnData;

	}

    public function getSize()
    {
        return $this->hasOne(VariantValue::class,'id', 'size_variant_value_id')->where('variant_id','2');
    }

    public function getColorDetail()
    {
        return $this->hasOne(VariantValue::class,'id', 'color_variant_value_id')->where('variant_id','1');
    }

    public function ProductVariantCombination()
    {
        return $this->belongsTo(ProductVariantCombination::class, 'id');
    }
}// end EmailAction class
