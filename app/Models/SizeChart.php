<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

class SizeChart extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function getFileAttribute($value = ""){
        if($value != "" && File::exists(Config('constant.SIZECHART_IMAGE_ROOT_PATH').$value)){
        $value = Config('constant.SIZECHART_IMAGE_PATH').$value;
        }
        return $value;
    }

    public function sizeChart()
{
    return $this->belongsTo(SizeChart::class);
}

}
