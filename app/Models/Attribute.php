<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attributes';

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', 0);
    }
    public function attribute_value()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}