<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    use HasFactory;
    protected $table = 'refund_requests';

    protected $guarded = ['id'];

    public function admin() {
        return $this->belongsTo(User::class,'updated_by','id');
    }
}
