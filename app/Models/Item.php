<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TeamScope;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    use HasFactory, TeamScope;

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'user_id',
        'sku',
        'name',
        'image',
        'cost',
        'price',
        'type',
        'brand',
        'quantity'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function analytics()
    {
        return $this->hasOne(Analytics::class);
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->sku)) {
                $item->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });
    }
}
