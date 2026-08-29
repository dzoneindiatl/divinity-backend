<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;

class Testimonial extends Model
{
    use HasFactory;
    public $table = 'testimonials';

    function getActiveTestimonial($searchData=array()){
        $Testimonial                 =  Testimonial::query();

        $all_testimonials 			=   $Testimonial->where('testimonials.is_active', 1)
                                            ->select('testimonials.id','testimonials.name','testimonials.description','testimonials.city','testimonials.image','testimonials.rating')->orderBy("testimonials.created_at","DESC")->get()->toArray();

        return $all_testimonials;
    }

    function getImageAttribute($value = ""){
        if($value != "" && File::exists(Config('constant.TESTIMONIAL_IMAGE_ROOT_PATH').$value)){
            return  Config('constant.TESTIMONIAL_IMAGE_URL').$value;
        }else {
            return Config('constant.IMAGE_URL') . "noimage.png";
        }
    }
}
