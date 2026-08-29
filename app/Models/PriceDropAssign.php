<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class PriceDropAssign extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function price_drop(){
        return $this->belongsTo(PriceDrop::class,'price_drop_id','id');
    }

}
