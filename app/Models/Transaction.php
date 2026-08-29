<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const ORDER_TYPE = 'order';
    const WALLET_TYPE = 'wallet';
    const SUCCESS = 'success';
    const FAILED = 'failed';
    const REFUNDED = 'refunded';

}
