<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class ShippingCharges extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'shipping_charges';
    public function country() {
        return $this->belongsTo(Country::class);
    }
    public function state() {
        return $this->belongsTo(State::class);
    }


    public function shippingzone() {
        return $this->belongsTo(ShippingZone::class);
    }
    public function zone()
    {
       // return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}
