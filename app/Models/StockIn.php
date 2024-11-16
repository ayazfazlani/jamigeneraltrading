<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockIn extends Model
{
    use HasFactory;
    protected $guarded = [];
    // protected $fillable = ['item_id', 'quantity', 'cost_per_unit', 'date'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
