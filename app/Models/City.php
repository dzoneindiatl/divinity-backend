<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
   
    public function user_address(){
        return $this->belongsTo(UserAddress::class,'id','city_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}