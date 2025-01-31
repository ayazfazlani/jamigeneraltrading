<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Transaction;
use App\Services\AnalyticsService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemsImport implements ToModel
{
    public function model(array $row)
    {
        $teamId = Auth::user()->getCurrentTeamId();

        // Create and save the item
        $item = new Item([
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'name' => $row[0],
            'quantity' => $row[1],
            'team_id' => $teamId,
            'cost' => $row[2],
            'price' => 0,
        ]);
        $item->save();

        // Update analytics
        $analyticsService = new AnalyticsService();
        $analyticsService->updateAllAnalytics($item, $item->quantity, 'created');

        // Log transaction
        $this->logTransaction($item, 'created', $item->quantity);

        return $item;
    }

    private function logTransaction($item, $type, $quantity)
    {
        $teamId = Auth::user()->getCurrentTeamId();

        Transaction::create([
            'item_id' => $item->id,
            'user_id' => Auth::user()->id,
            'team_id' => $teamId,
            'item_name' => $item->name,
            'type' => $type,
            'quantity' => $quantity,
            'unit_price' => $item->cost,
            'total_price' => $item->cost * $quantity,
            'date' => now(),
        ]);
    }
}
