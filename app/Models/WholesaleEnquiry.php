<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleEnquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'city', 'company_name', 'gst_number', 'message'
    ];
}
?>