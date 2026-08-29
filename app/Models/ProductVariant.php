<?php 
namespace App\Models; 
use Eloquent;
/**
 * Review Model
 */
class ProductVariant extends Eloquent {
    protected $table = 'product_variants';

        protected $fillable = [
        'product_id',
        'variant_id',
    ];
    protected $guarded = [];
	
	public function variant() {
        return $this->belongsTo(Variant::class);
    }
    
    public function variantValues() {
        return $this->hasMany(ProductVariantValue::class, 'product_variant_id', 'id');
    }
}
