<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VariantValue;
/**
 * Review Model
 */
class Variant extends Model {

	
/**
 * The database table used by the model.
 *
 * @var string
 */
	protected $table = 'variants';
	
	protected $fillable = ['name'];

	public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function variant_values(){
        return $this->hasMany(VariantValue::class, 'variant_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', 0);
    }
}