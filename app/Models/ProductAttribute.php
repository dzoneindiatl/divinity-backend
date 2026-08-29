<?php 
namespace App\Models; 
use Eloquent;

class ProductAttribute extends Eloquent {
    protected $table = 'product_attributes';

    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'attribute_value_id',
        "attribute_id"
    ];

	public function attribute() {
        return $this->belongsTo(Attribute::class);
    }
    
    public function productAttributeValues() {
        return $this->hasMany(ProductAttributeValue::class);
    }		

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_attribute_id');
    }
    
}