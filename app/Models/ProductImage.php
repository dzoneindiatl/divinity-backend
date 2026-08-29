<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Review Model
 */
class ProductImage extends Model
{

	use HasFactory;

	protected $table = 'product_graphics';

	protected $fillable = ['product_id', 'variant_id', 'product_type', 'graphic_type', 'g_extention', 'g_size', 'g_length', 'status', 'graphic'];

	protected $dates = ['created_at', 'updated_at'];

	public function getGraphicAttribute($value)
	{
		if (!empty($value)) {
			//return asset('uploads/products/' . $value); // Assuming images are stored in /public/uploads/
			return env('WEBSITE_URL').'uploads/products/'.$value;
		} else {
			return asset('images/noimage.png'); // Default image
		}
	}
} // end EmailAction class
