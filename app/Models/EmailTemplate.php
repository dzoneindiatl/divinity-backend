<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;
    protected $table="email_templates";
    public function email_action(){
        return $this->belongsTo(EmailAction::class,'action_id','id');
    }
}
