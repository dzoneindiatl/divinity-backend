<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = 'states';
    
    protected $casts = [
        'weight_ranges' => 'array',
    ];
    public function user_address(){
        return $this->belongsTo(UserAddress::class,'id','state_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

 

}