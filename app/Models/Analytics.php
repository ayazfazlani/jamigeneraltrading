<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'current_quantity',
        'inventory_assets',
        'average_quantity',
        'turnover_ratio',
        'stock_out_days_estimate',
        'total_stock_out',
        'avg_daily_stock_in',
        'avg_daily_stock_out'
    ];


    public function items()
    {
        return $this->belongsTo(Item::class);
    }
}
