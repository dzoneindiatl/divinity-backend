<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    const RECEIVED = 'received';
    const CONFIRMED = 'confirmed';
    const SHIPPED = 'shipped';
    const OUT_FOR_DELIVERY = 'out_for_delivery';
    const DELIVERED = 'delivered';
    const CANCELLED = 'cancelled';
    const RETURNED = 'returned';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function itemTax()
    {
        return $this->hasOne(OrderItemTax::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class,'updated_by');
    }

    public function orderStatusHistory() {
        return $this->hasMany(OrderStatusHistory::class, 'order_item_id');
    }

    public function refundRequest() {
        return $this->hasOne(RefundRequest::class);    
    }

    public function cancelRequest() {
        return $this->hasOne(OrderCancellation::class);
    }

    public function courier() {
        return $this->belongsTo(Couriers::class,'courier_id'); 
    }
}