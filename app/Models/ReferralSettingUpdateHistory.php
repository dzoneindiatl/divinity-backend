<?php

namespace App\Models;
Use File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSettingUpdateHistory extends Model
{
    use HasFactory;
    public $table = 'referral_setting_update_histories';

    
	public function user()
    {
         return $this->belongsTo(User::class);
    }
	
	public function getUserCreated()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
	
	public function getUserUpdated()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
	
	public function getUpdatedRow()
    {
       return $this->hasMany(self::class, 'updated_referal_id');
	}
   
}
