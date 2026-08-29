<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Review Model
 */
class AttributeValue extends Model
{
    protected $table = 'attribute_values';
    protected $fillable = ['attribute_id', 'name', 'is_deleted'];
}