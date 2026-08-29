<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySpecification extends Model
{
    protected $table = 'category_specifications';

    protected $fillable = [
        'category_id',
        'specification_id',
    ];
}
