<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

// class StockIn extends Model
// {
//     use HasFactory;
//     protected $guarded = [];
//     // protected $fillable = ['item_id', 'quantity', 'cost_per_unit', 'date'];

//     public function item()
//     {
//         return $this->belongsTo(Item::class);
//     }

//     public function transaction()
//     {
//         return $this->hasOne(Transaction::class);
//     }
// }




namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\AnalyticsService;

class StockIn extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected static $analyticsService;

    // Boot method to set up model events
    protected static function boot()
    {
        parent::boot();

        // Initialize the AnalyticsService
        static::$analyticsService = app(AnalyticsService::class);

        // Handle the "created" event
        static::created(function ($stockIn) {
            $stockIn->updateAnalytics('stock_in');
        });

        // Handle the "updated" event
        static::updated(function ($stockIn) {
            $stockIn->updateAnalytics('stock_in');
        });

        // Handle the "deleted" event (if necessary)
        static::deleted(function ($stockIn) {
            $stockIn->updateAnalytics('stock_in', true);
        });
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Update analytics data for the associated item.
     *
     * @param string $operation - The operation type ('stock_in' or 'stock_out').
     * @param bool $isDeleted - If the stock-in record is being deleted.
     * @return void
     */
    public function updateAnalytics(string $operation, bool $isDeleted = false)
    {
        $item = $this->item;

        if ($item) {
            $quantity = $isDeleted ? -$this->quantity : $this->quantity;
            static::$analyticsService->updateAllAnalytics($item, $quantity, $operation);
        }
    }
}
