<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pincodes extends Model
{
    use HasFactory;

     public $table = 'pincodes';
     protected $fillable = [
        'pincode',
        'delivery_type',
        'extra_delivery_charge',
        'city_id',
        'state_id',
        'country_id',
        'status',
    ];
    public function country() {
        return $this->belongsTo(Country::class,'country_id');
    }

    public function state() {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }


}
