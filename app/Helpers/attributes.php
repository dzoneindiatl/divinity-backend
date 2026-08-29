<?php

// function getAttribute(){
//     $attributes = Attribute::where('is_active', 1)->where('is_deleted', 0)->get();
//     return $attributes;
// }


namespace App\Helpers;

use App\Models\Attribute;
class Attributes
{
    public static function getAttribute()
    {
        $attributes = Attribute::where('is_active', 1)->where('is_deleted', 0)->get();
        return $attributes;
    }

    public static function fixCategoryId($categoryId)
    {
        if (!empty($categoryId)) {
            if (is_array($categoryId)) {
                return $categoryId;
            }
            if (is_array($categoryId) && isset($categoryId[0]) && is_string($categoryId[0])) {
                $categoryId = $categoryId[0];
            }
            $decoded = json_decode($categoryId, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return [];
    }
}
