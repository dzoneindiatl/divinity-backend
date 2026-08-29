<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGraphics extends Model
{
    use HasFactory;

    protected $table = 'product_graphics';

    protected $fillable = ['is_front','is_back','is_variant_icon','product_id', 'variant_id', 'product_type', 'graphic_type', 'g_extention', 'g_size', 'g_length', 'status', 'graphic'];

    protected $dates = ['created_at', 'updated_at'];
}
