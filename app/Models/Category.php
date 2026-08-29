<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')
            ->where('is_active', 1)
            ->where('is_deleted', 0);
    }

    public function parentcategory()
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }
    public function superparentcategory()
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }
    // newly added

    public function ancestors()
    {
        $ancestors = collect([]);
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent); // prepend to maintain the order from root to child
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    // newly added


    function getImageAttribute($value = "")
    {
        if ($value != "" && File::exists(Config('constant.CATEGORY_IMAGE_ROOT_PATH') . $value)) {
            // return  Config('constant.CATEGORY_IMAGE_URL') . $value;
            return $value;
        }
    }

    public function getActiveCategories()
    {
        return self::whereNull('parent_id')
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->select('id', 'parent_id', 'name', 'slug', 'description', 'image', 'thumbnail_image', 'video', 'category_order')
            ->orderBy('category_order', 'ASC')
            ->with('subcategories') // Eager loading subcategories
            ->limit(8)
            ->get(); // Now returns a collection, not an array
    }

    public function getAllCategories()
    {
        return self::whereNull('parent_id')
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->select('id', 'parent_id', 'name', 'slug', 'description', 'image', 'thumbnail_image', 'video', 'category_order')
            ->orderBy('category_order', 'ASC')
            // ->with('subcategories') // Eager loading subcategories
            ->limit(8)
            ->get(); // Now returns a collection, not an array
    }


    // public function subcategories()
    // {
    //     return $this->hasMany(Category::class, 'parent_id');
    // }

    // App\Models\Category.php

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->orderBy('category_order', 'ASC')
            ->with('subcategories'); // 👈 recursive relation
    }



    function getThumbnailImageAttribute($value = "")
    {
        if ($value != "" && File::exists(Config('constant.CATEGORY_IMAGE_ROOT_PATH') . $value)) {
            return  Config('constant.CATEGORY_IMAGE_URL') . $value;
        }
    }

    function getVideoAttribute($value = "")
    {
        if ($value != "" && File::exists(Config('constant.CATEGORY_VIDEO_ROOT_PATH') . $value)) {
            return  Config('constant.CATEGORY_VIDEO_URL') . $value;
        }
    }


    public function variants()
    {
        return $this->hasMany(CategoryVariant::class);
    }

    public function attributes()
    {
        return $this->hasMany(CategoryAttribute::class);
    }

    public function specifications()
    {
        return $this->hasMany(CategorySpecification::class);
    }

    public function taxes()
    {
        return $this->hasMany(CategoryTax::class);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'created_role_by');
    }
}