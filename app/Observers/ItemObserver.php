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
        $analytics->item_name = $item->name;
        $analytics->current_quantity = $item->quantity;
        $analytics->inventory_assets = $item->quantity * $item->price;
        $analytics->save();
    }

    /**
     * Handle the Item "updated" event.
     */
    public function updated(Item $item): void
    {
        $analytics = Analytics::where('item_id', $item->id)->first();

        if ($analytics) {
            $analytics->current_quantity = $item->quantity;
            $analytics->inventory_assets = $item->quantity * $item->price;
            $analytics->save();
        }
    }

    /**
     * Handle the Item "deleted" event.
     */
    public function deleted(Item $item): void
    {
        Analytics::where('item_id', $item->id)->delete();
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
}
