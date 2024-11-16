<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\Analytics;

class ItemObserver
{
    /**
     * Handle the Item "created" event.
     */
    public function created(Item $item): void
    {
        $analytics = new Analytics();
        $analytics->item_id = $item->id;
        $analytics->current_quantity = $item->quantity;
        $analytics->inventory_assets = $item->quantity * $item->price; // Assuming you have a price field
        $analytics->save();
    }

    /**
     * Handle the Item "updated" event.
     */
    public function updated(Item $item): void
    {
        $analytics = Analytics::where('item_id', $item->id)->first();

        if ($analytics) {
            // Update quantity and inventory assets based on the latest item data
            $analytics->current_quantity = $item->quantity;
            $analytics->inventory_assets = $item->quantity * $item->price;

            // Update analytics calculations
            $analytics->avg_daily_stock_in = $this->calculateAverageDailyStockIn($item);
            $analytics->avg_daily_stock_out = $this->calculateAverageDailyStockOut($item);

            $analytics->save();
        }
    }



    /**
     * Handle the Item "deleted" event.
     */
    public function deleted(Item $item): void
    {
        //
    }

    /**
     * Handle the Item "restored" event.
     */
    public function restored(Item $item): void
    {
        //
    }

    /**
     * Handle the Item "force deleted" event.
     */
    public function forceDeleted(Item $item): void
    {
        //
    }



    protected function calculateAverageDailyStockIn(Item $item)
    {
        // Custom calculation logic for average daily stock in
        return /* Your Calculation */;
    }

    /**
     * Calculate the average daily stock out based on custom logic.
     */
    protected function calculateAverageDailyStockOut(Item $item)
    {
        // Custom calculation logic for average daily stock out
        return /* Your Calculation */;
    }
}
