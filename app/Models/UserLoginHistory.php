<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip',
        'login_time'
    ];
 
    public $timestamps = false;
}
