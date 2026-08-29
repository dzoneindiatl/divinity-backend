<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseEnquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'city', 'space', 'investment', 'message'
    ];
}
?>