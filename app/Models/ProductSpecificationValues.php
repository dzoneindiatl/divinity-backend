<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecificationValues extends Model
{
    protected $table = 'product_specification_values';

    protected $fillable = [
        'product_id',
        'specification_id',
        'value',
    ];

    // 🔗 Specification relation
    public function specification()
    {
        return $this->belongsTo(ProductSpecification::class, 'specification_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
