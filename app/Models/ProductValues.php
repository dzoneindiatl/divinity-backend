<?php

namespace App\Models;

use App\Models\ProductOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductValues extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public function productOption()
    {
        return $this->belongsTo(ProductOptions::class,'product_option_id','id');
    }
}
