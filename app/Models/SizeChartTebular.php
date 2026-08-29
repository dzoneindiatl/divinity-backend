<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeChartTebular extends Model
{
    use HasFactory;
    public $table = 'size_chart_tebulars';
    protected $guarded = ['id'];
    protected $fillable = [];
}
