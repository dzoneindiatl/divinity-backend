<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;

class Banner extends Model
{
    use HasFactory;
    public $table = 'banners';
    protected $guarded = ['id'];

    function getActiveSliders()
    {

        $all_sliders = Banner::where('banners.is_active', 1)->where('banners.is_deleted', 0)
            ->select('banners.id', 'banners.description', 'banners.image', 'banners.video', 'banners.url', 'banners.height', 'banners.width', 'banners.order_number')->get()->toArray();
        return $all_sliders;
    }

    function getTopActiveSliders()
    {
        $all_top_sliders = Banner::where('banners.is_active', 1)->where('banners.is_deleted', 0)
            ->where('banners.order_number', 1)
            ->select('banners.id', 'banners.description', 'banners.image', 'banners.video', 'banners.url', 'banners.height', 'banners.width', 'banners.order_number')->get()->toArray();
        return $all_top_sliders;
    }

    function getMiddleActiveSliders()
    {
        $all_top_sliders             =   Banner::where('banners.is_active', 1)->where('banners.is_deleted', 0)
            ->where('banners.order_number', 2)
            ->select('banners.id', 'banners.description', 'banners.image', 'banners.video', 'banners.url', 'banners.height', 'banners.width', 'banners.order_number')->get()->toArray();

        return $all_top_sliders;
    }

    function getImageAttribute($value = "")
    {
        if ($value != "" && File::exists(Config('constant.BANNER_IMAGE_ROOT_PATH') . $value)) {
            return  Config('constant.BANNER_IMAGE_URL') . $value;
        }
    }

    function getVideoAttribute($value = "")
    {
        if ($value != "" && File::exists(Config('constant.BANNER_VIDEO_ROOT_PATH') . $value)) {
            return  Config('constant.BANNER_VIDEO_URL') . $value;
        }
    }
}