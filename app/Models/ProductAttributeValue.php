<?php 
namespace App\Models; 
use Eloquent;

class ProductAttributeValue extends Eloquent {
	protected $table = 'product_attribute_values';

    protected $fillable = [
        'product_attribute_id',
        'status',
        'product_id',
        "attribute_value_id"
    ];
	
	public function attributeValue() {
        return $this->belongsTo(AttributeValue::class);
    }
}
