<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpleVeriantValue extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table='simple_variant_values';
}
