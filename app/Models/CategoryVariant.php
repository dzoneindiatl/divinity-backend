<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Review Model
 */
class CategoryVariant extends Model
{


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'category_variants';

	protected $fillable = ['category_id', 'variant_id'];
	public function variant()
	{
		return $this->belongsTo(Variant::class);
	}
}// end EmailAction class