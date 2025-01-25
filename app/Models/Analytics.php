<?php

namespace App\Models;

use App\Traits\TeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Analytics extends Model
{
    use HasFactory, TeamScope;

    protected $guarded = [];

    protected $casts = [
        'item_id' => 'integer', // Assuming item_id is an integer
        'item_name' => 'string',
        'current_quantity' => 'integer', // Assuming current_quantity is an integer
        'inventory_assets' => 'decimal:2', // 15 digits, 2 after the decimal point
        'average_quantity' => 'decimal:2', // 15 digits, 2 after the decimal point
        'turnover_ratio' => 'decimal:2', // 15 digits, 2 after the decimal point
        'stock_out_days_estimate' => 'integer', // Assuming it's an integer
        'total_stock_out' => 'integer', // Assuming it's an integer
        'total_stock_in' => 'integer', // Assuming it's an integer
        'avg_daily_stock_in' => 'decimal:2', // 15 digits, 2 after the decimal point
        'avg_daily_stock_out' => 'decimal:2', // 15 digits, 2 after the decimal point
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Add team relationship
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    protected static function booted()
    {
        static::creating(function ($analytics) {
            if ($analytics->item) {
                $analytics->team_id = $analytics->item->team_id;
                $analytics->user_id = $analytics->item->user_id;
            }
        });
    }
}
