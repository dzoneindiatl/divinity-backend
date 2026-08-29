<?php

namespace App\Models;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChildCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public function scopeGetChildCategoryByCategory($query, $subCategoryId = null)
    {
        return $query->where('sub_category_id', $subCategoryId);
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id','id');
    }
}
