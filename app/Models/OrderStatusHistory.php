<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    //protected $guarded = ['id']; 
    protected $table = "order_status_history";
    protected $guarded = ['id'];

    public function order()
    {
        return $this->hasMany(Order::class);
        // return $this->belongsTo(Order::class);
    }
}
