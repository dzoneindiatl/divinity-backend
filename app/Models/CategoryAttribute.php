<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    use HasFactory;

    protected $table = 'category_attributes';

	  protected $fillable=['attribute_id','category_id'];

	public function attribute()
	{
		return $this->belongsTo(Attribute::class);
	}
}
