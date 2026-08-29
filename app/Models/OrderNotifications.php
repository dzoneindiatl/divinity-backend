<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class OrderNotifications extends Model
{
    use HasFactory;

    //protected $guarded = ['id']; 
    protected $table="order_notification";
     
    public function order() {
        return $this->hasMany(Order::class);
    }
}