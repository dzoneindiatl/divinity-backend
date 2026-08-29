<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function customers()
    {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    public function couponUses()
    {
        return $this->hasMany(CouponUse::class);
    }

    public function couponUsers()
    {
        return $this->hasMany(CouponUser::class);
    }

}
