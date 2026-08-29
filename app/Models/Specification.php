<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Specification Model
 */
class Specification extends Model
{
    protected $table = 'specifications';

   
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

	public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', 0);
    }
}
