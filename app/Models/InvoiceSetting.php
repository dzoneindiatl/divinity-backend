<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    use HasFactory;
    protected $table = 'invoice_settings';

    protected $fillable = [
        'country_id',
        'state_id',
        'city_id',
        'pincode',
        'address',
        'prefix',
        'name',
        'cash_on_limit',
        'order_prefix',
        'invoice_number',
        'nature_spilly',
        'packet_id',
        'website_name',
        'signature',
        'designation',
        'note',
        'is_active',
        'invoice_setting',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}