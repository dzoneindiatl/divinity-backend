<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Facades\File;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getImageAttribute($value = "")
    {
        if ($value != "" && File::exists(Config('constant.USER_IMAGE_ROOT_PATH') . $value)) {
            $value = Config('constant.USER_IMAGE_PATH') . $value;
        }
        return $value;
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id', 'id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id')->orderBy('created_at', 'desc');
    }
    public function refundedhistorys()
    {
        return $this->hasMany(RefundedHistory::class, 'user_id', 'id')->orderBy('created_at', 'desc');
    }
    public function referralhistorys()
    {
        return $this->hasMany(ReferralHistory::class, 'referral_by', 'id')->orderBy('created_at', 'desc');
    }
    public function debitWalletHistorys()
    {
        return $this->hasMany(WalletHistory::class, 'user_id', 'id')
            ->where('type', 'debit');
    }

    public function getDebitTotalAmountAttribute()
    {
        return $this->debitWalletHistorys()->sum('amount');
    }
    public function refundCreditHistorys()
    {
        return $this->hasMany(WalletHistory::class, 'user_id', 'id')
            ->where('type', 'credit')
            ->where('description', 'like', '%refund%')
            ->orderBy('created_at', 'desc');
    }
    public function price_drop_logs()
    {
        return $this->hasMany(PriceDropLog::class, 'user_id', 'id');
    }

    public function billingAddress()
    {
        return $this->hasOne(UserAddress::class, 'id', 'billing_address_id');
    }

    public function shippingAddress()
    {
        return $this->hasOne(UserAddress::class, 'id', 'shipping_address_id');
    }

    public function address()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'user_role_id');
    }
}