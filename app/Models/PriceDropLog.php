<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceDropLog extends Model
{
    use HasFactory;

    protected $table="price_drop_logs";

    public function users(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function price_drop(){
        return $this->belongsTo(PriceDrop::class,'price_drop_id','id');
    }
}
