<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $table = 'user_addresses';

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // public function city(){
    //     return $this->hasOne(City::class,'city_id','id');
    // }
    // public function state(){
    //     return $this->hasOne(State::class,'state_id','id');
    // }
    // public function country(){
    //     return $this->hasOne(Country::class,'country_id','id');
    // }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    public static function getShippingAddress($type, $address_id){
        $add = array();
        $address = UserAddress::find($address_id);
        if(!empty($address)){
            $add[$type.'_customer_name'] = $address->name;
            $add[$type.'_last_name'] = $address->name;
            $add[$type.'_address'] = $address->address;
            $add[$type.'_city'] = $address->city->name;
            $add[$type.'_pincode'] = $address->postal_code;
            $add[$type.'_state'] = $address->state->name;
            $add[$type.'_country'] = $address->country->name;
            $add[$type.'_email'] = $address->email;
            $add[$type.'_phone'] = $address->phone_number;
        }
        return $add;
    }

    public static function getShippingAddressByUserID($type, $user_id){
        $add = array();
        $address = UserAddress::where('user_id',$user_id)->where('type',$type)->first();
        if(!empty($address)){
            $add[$type.'_customer_name'] = $address->name;
            $add[$type.'_last_name'] = $address->name;
            $add[$type.'_address'] = $address->address;
            $add[$type.'_city'] = $address->city->name;
            $add[$type.'_pincode'] = $address->postal_code;
            $add[$type.'_state'] = $address->state->name;
            $add[$type.'_country'] = $address->country->name;
            $add[$type.'_email'] = $address->email;
            $add[$type.'_phone'] = $address->phone_number;
        }
        return $add;
    }
}
