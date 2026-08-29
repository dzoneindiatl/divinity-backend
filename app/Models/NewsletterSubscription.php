<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    public $table = 'newsletter_subscriptions';
    
    protected $fillable = ['email','user_id','subscribed_at','status','unsubscribed_at','source'];
}
