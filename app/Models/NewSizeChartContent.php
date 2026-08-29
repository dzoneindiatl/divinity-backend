<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewSizeChartContent extends Model
{
    use HasFactory;
    public $table = 'new_size_chart_contents';
    protected $guarded = ['id'];
    protected $fillable = [
        'description',
    ];
}
