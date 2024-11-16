<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockOut extends Model
{
    use HasFactory;
    protected $guarded = [];
    // protected $fillable = ['item_id', 'quantity', 'price_per_unit', 'date'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
