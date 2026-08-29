<?php

namespace App\Models;
use Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class ProductColorImage extends Eloquent
{
    protected $table = 'product_color_images';
    use HasFactory;
    protected $guarded = ['id'];

}
