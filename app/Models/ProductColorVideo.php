<?php
namespace App\Models;
use Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class ProductColorVideo extends Eloquent
{
    protected $table = 'product_color_videos';
    use HasFactory;
    protected $guarded = ['id'];

}
