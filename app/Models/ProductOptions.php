<?php

namespace App\Models;

use App\Models\ProductValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOptions extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public function productOptionValues()
    {
        return $this->hasMany(ProductValues::class,'product_option_id','id');
    }
}
