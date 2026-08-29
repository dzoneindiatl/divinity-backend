<?php

namespace App\Models;
Use File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralHistory extends Model
{
    use HasFactory;
    public $table = 'referral_histories';

    
 public function user()
    {
         return $this->belongsTo(User::class);
    }
   
}
