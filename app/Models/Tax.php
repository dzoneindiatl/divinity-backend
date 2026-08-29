<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_taxes')
            ->withPivot(['tax_option', 'tax_type'])
            ->withTimestamps();
    }
}