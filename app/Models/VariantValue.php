<?php 
namespace App\Models; 
use Eloquent;
/**
 * Review Model
 */
class VariantValue extends Eloquent {

	
/**
 * The database table used by the model.
 *
 * @var string
 */
	protected $table = 'variant_values';
	protected $fillable = ['variant_id', 'name'];

	public function productVariantValues()
    {
        return $this->hasMany(ProductVariantValue::class, 'variant_value_id');
    }
	
}// end EmailAction class
