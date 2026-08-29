<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couriers extends Model
{
    use HasFactory;

     public $table = 'couriers';
     protected $fillable = [
        'name',
        'slug',
        'tracking_url',
    ];



}
