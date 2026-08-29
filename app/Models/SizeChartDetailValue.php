<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use File;
use Illuminate\Database\Eloquent\Model;

class SizeChartDetailValue extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'table_headers',
        'table_data',
    ];

    protected $casts = [
        'table_headers' => 'array',
        'table_data' => 'array',
    ];

}
