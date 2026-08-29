<?php

namespace App\Models;
Use File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundedHistory extends Model
{
    use HasFactory;
    public $table = 'refunded_histories';

    
 public function user()
    {
         return $this->belongsTo(User::class);
    }
   
    public function product()
    {
         return $this->belongsTo(Product::class);
    }
    public function order()
    {
         return $this->belongsTo(Order::class);
    }
    public function orderItem()
    {
         return $this->belongsTo(OrderItem::class);
    }
}
