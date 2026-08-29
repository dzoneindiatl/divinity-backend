<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAction extends Model
{
    use HasFactory;
    protected $table="email_actions";
    public function email_templates(){
        return $this->hasMany(EmailTemplate::class,'action_id','id');
    }
    public function email_action_options(){
        return $this->hasMany(EmailTemplate::class,'action_id','id');
    }
}
