<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function getStatusAccrdingtoOrderstatusForAdmin(){
        return [
            "pending"=>[
                "pending"=>"Pending",
                "accepted"=>"Accepted",
                "cancelled"=>"Cancel"
            ],
            "accepted"=>[
                "accepted"=>"Accepted",
                "processing"=>"Processing",
                "cancelled"=>"Cancel"
            ],
            "processing"=>[
                "processing"=>"Processing",
                "shipped"=>"Shipped",
                "cancelled"=>"Cancel"
            ],
            "shipped"=>[
                "shipped"=>"Shipped",
                "in-transit"=>"In Transit",
                "out-for-delivery"=>"Out for Delivery",
                "delivered"=>"Delivered"
            ], 
            "in-transit"=>[
                "in-transit"=>"In Transit",
                "out-for-delivery"=>"Out for Delivery",
                "delivered"=>"Delivered"
            ],
            "out-for-delivery"=>[
                "out-for-delivery"=>"Out for Delivery",
                "delivered"=>"Delivered"
            ],             
            "delivered"=>[
                "delivered"=>"Delivered",
                "return-accepted"=>"Return Accepted",
                "return-rejected"=>"Return Rejected"
            ],
            "return-accepted"=>[
                "return-accepted"=>"Return Accepted",
                "refund-pending"=>"Refund Pending",
                "refunded"=>"Refunded"
            ], 
            "refund-pending"=>[
                "refund-pending"=>"Refund Pending",
                "refunded"=>"Refunded"
            ],           
        ];
    }

    public static function getStatusAccrdingtoOrderstatusForCustomer(){
        return [
            "pending"=>[
                "pending"=>"Pending",
                "cancelled_by_customer"=>"Cancelled By Customer"
            ],
            "accepted"=>[
                "accepted"=>"Accepted",
                "cancelled_by_customer"=>"Cancelled By Customer"
            ],
            "processing"=>[
                "processing"=>"Processing",
                "cancelled_by_customer"=>"Cancelled By Customer"
            ],
            "delivered"=>[
                "delivered"=>"Delivered",
                "return-requested"=>"Return Requested"
            ]
         ];
    }
    
    public static function getAllStatus(){
        return Cache::remember('order_statuses', 60*60, function() {
            return OrderStatus::pluck('name','slug')?->toArray();
        });
    }

    public static function getAllStatusId(){
        return Cache::remember('order_statuses_id', 60*60, function() {
            return OrderStatus::pluck('slug','id')?->toArray();
        });
    }

    public static function getBasicStatus(){
        return Cache::remember('order_statuses_basic', 60*60, function() {
            return OrderStatus::whereIn('slug',['received','completed', 'captured','cancelled', 'confirmed'])->pluck('name','slug')?->toArray();
        });
        /* return [
            "received"=>"Received",
            "COMPLETED"=>"Completed",
            "captured"=>"Captured",
            //"pending"=>"Pending",            
            "cancelled"=>"Cancelled",
            "confirmed"=>"Confirmed"           
        ]; */
    }

    public static function getShippedStatus()
    {
        return Cache::remember('order_statuses_shipped', 60*60, function() {
            return OrderStatus::whereIn('slug',['shipped','out-for-delivery', 'delivered'])->pluck('name','slug')?->toArray();
        });
        /* return [
            "shipped"=>"Shipped",
            "outfordelivery"=>"Out for delivery",
            "delivered"=>"Delivered"         
        ]; */
    }

    public static function getExchangedStatus()
    {
        return [
            //"exchanged"=>"Exchanged",               
            //"returned"=>"Returned",
            "refunded"=>"Refunded"           
        ];
    }

    
  
    public function latest_item() {
        return $this->hasOne(OrderItem::class)->latest();
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class,'address_id','id');
    }
    public function currency(){
        return $this->hasOne(Currency::class,'currency_code','currency_code');
    }

    public function getAddressForInvoice($address_id) {
        $address = UserAddress::find($address_id);
        $string = "N/A";
        if(!empty($address)){
            $string = "<strong>".ucfirst($address->name)."</strong><br>".$address->address."<br>";
            if(!empty($address->landmark)){
                $string .= $address->landmark."<br>";
            }
            $string .= $address->city->name.", ".$address->postal_code."<br>".$address->state->name."<br>".$address->country->name."<br>Contact: ".$address->phone_number."<br>Email: ".$address->email;
        }
        return $string;
    }
    public function billingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }
    
    public function shippingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }
    
    public function orderItem() {
        return $this->hasMany(OrderItemTax::class, 'order_item_id');
    }

    public function courier() {
        return $this->belongsTo(Couriers::class,'delivery_partner_name'); 
    }
}
