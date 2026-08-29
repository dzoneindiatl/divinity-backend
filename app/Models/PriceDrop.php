<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class PriceDrop extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function price_drop_assigns(){
        return $this->hasMany(PriceDropAssign::class, 'price_drop_id', 'id');
    }
    public function price_drop_logs(){
        return $this->hasMany(PriceDropLog::class,'price_dorp_id','id');
    }

}
