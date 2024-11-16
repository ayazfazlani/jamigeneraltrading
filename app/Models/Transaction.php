<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Assuming this will store the type of transaction: 'added', 'updated', 'deleted'
    protected $casts = [
        'type' => 'string',  // 'added', 'updated', 'deleted'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }
}
