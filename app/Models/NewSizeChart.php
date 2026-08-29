<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class NewSizeChart extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = [
        'category', 'size', 'chest', 'waist', 'hip', 'shoulder',
        'armhole', 'sleeve_length', 'length'
    ];

    public static function getFileAttribute($value = ""){
        if($value != "" && File::exists(Config('constant.SIZECHART_IMAGE_ROOT_PATH').$value)){
        $value = Config('constant.SIZECHART_IMAGE_PATH').$value;
        }
        return $value;
    }

    public function newSizeChart()
    {
        return $this->belongsTo(NewSizeChart::class);
    }

}
