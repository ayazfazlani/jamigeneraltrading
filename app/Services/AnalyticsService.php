<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Analytics;
use Illuminate\Support\Facades\Auth;

class AnalyticsService
{
  /**
   * Update all analytics related to the item.
   *
   * @param Item $item
   * @param int $quantity
   * @param string $operation
   * @return void
   */
  public function updateAllAnalytics(Item $item, $quantity, $operation)
  {
    // Find or create the analytics record for the item
    $analytics = Analytics::firstOrCreate(
      ['item_id' => $item->id],
      [
        'item_name' => $item->name,
        'current_quantity' => 0,
        'team_id' => Auth::user()->getCurrentTeamId(),
        'inventory_assets' => 0,
        'average_quantity' => 0,
        'turnover_ratio' => 0,
        'stock_out_days_estimate' => 0,
        'total_stock_out' => 0,
        'total_stock_in' => 0,
        'avg_daily_stock_in' => 0,
        'avg_daily_stock_out' => 0
      ]
    );

    // Update analytics based on the operation type
    switch ($operation) {
      case 'created':
        // When an item is created, set initial values
        $analytics->current_quantity = $quantity;
        $analytics->inventory_assets = $item->cost * $quantity;
        break;

      case 'stock_in':
        $analytics->current_quantity += $quantity;
        $analytics->total_stock_in += $quantity;

        // Calculate days since first stock-in
        $firstStockInDate = $analytics->created_at ?? now();
        $daysSinceFirstStockIn = max($firstStockInDate->diffInDays(now()), 1);

        // Update avg daily stock-in
        $analytics->avg_daily_stock_in = $analytics->total_stock_in / $daysSinceFirstStockIn;
        break;

      case 'stock_out':
        // When stock-out operation happens, decrease quantity
        if ($analytics->current_quantity >= $quantity) {
          $analytics->current_quantity -= $quantity;
          $analytics->total_stock_out += $quantity;
        } else {
          throw new \Exception("Not enough stock to perform stock-out.");
        }
        break;

      case 'update':
        // When an item is updated, update the current quantity
        $analytics->current_quantity = $item->quantity;
        break;
    }

    // Update calculated analytics metrics
    $this->updateAnalyticsMetrics($analytics, $item, $quantity, $operation);

    // Save changes
    $analytics->save();
  }

  /**
   * Calculate and update turnover ratio, average daily stock-in/out, and stock-out days estimate.
   *
   * @param Analytics $analytics
   * @param Item $item
   * @param int $quantity
   * @param string $operation
   * @return void
   */
  private function updateAnalyticsMetrics(Analytics $analytics, Item $item, $quantity, $operation)
  {
    // Calculate days since first stock-in
    $firstStockInDate = $analytics->created_at ?? now();
    $daysSinceFirstStockIn = max($firstStockInDate->diffInDays(now()), 1);

    // Calculate days since first stock-out
    $firstStockOutDate = $analytics->updated_at ?? now(); // Assuming last update is stock-out date
    $daysSinceFirstStockOut = max($firstStockOutDate->diffInDays(now()), 1);

    // Avg daily stock-in
    if ($analytics->total_stock_in > 0) {
      $analytics->avg_daily_stock_in = $analytics->total_stock_in / $daysSinceFirstStockIn;
    }

    // Avg daily stock-out
    if ($analytics->total_stock_out > 0) {
      $analytics->avg_daily_stock_out = $analytics->total_stock_out / $daysSinceFirstStockOut;
    }

    // Calculate turnover ratio (example logic)
    if ($analytics->inventory_assets > 0) {
      $analytics->turnover_ratio = $analytics->total_stock_out / $analytics->inventory_assets;
    }

    // Estimate stock-out days (based on current stock and daily stock-out)
    if ($analytics->avg_daily_stock_out > 0) {
      $analytics->stock_out_days_estimate = $analytics->current_quantity / $analytics->avg_daily_stock_out;
    }
  }
}
