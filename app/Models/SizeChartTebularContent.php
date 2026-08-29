<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeChartTebularContent extends Model
{
    use HasFactory;
    public $table = 'size_chart_tebular_contents';
    protected $guarded = ['id'];
    protected $fillable = [];
}
