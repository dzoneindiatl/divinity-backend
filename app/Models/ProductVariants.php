<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductVariantValue;

class ProductVariants extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];


    public function variantValues()
    {
        return $this->hasMany(ProductVariantValue::class, 'product_variant_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
