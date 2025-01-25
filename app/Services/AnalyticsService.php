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
    $analytics = Analytics::where('item_id', $item->id)->first();

    if (!$analytics) {
      $analytics = new Analytics();
      $analytics->item_id = $item->id;
      $analytics->item_name = $item->name;
    }

    // Update analytics based on the operation type
    switch ($operation) {
      case 'created':
        // When an item is created, set initial values
        $analytics->current_quantity = $quantity;
        $analytics->team_id = auth()->user()->team_id;
        $analytics->user_id = Auth::user()->id;
        $analytics->inventory_assets = $item->cost * $quantity; // Set the inventory value
        $analytics->average_quantity = $quantity;
        $analytics->turnover_ratio = 0;
        $analytics->stock_out_days_estimate = 0;
        $analytics->total_stock_out = 0;
        $analytics->total_stock_in = 0;
        $analytics->avg_daily_stock_in = 0;
        $analytics->avg_daily_stock_out = 0;
        break;

      case 'stock_in':
        // When stock-in operation happens, increase quantity and other metrics
        $analytics->current_quantity += $quantity;
        $analytics->total_stock_in += $quantity;
        break;

      case 'stock_out':
        // When stock-out operation happens, decrease quantity
        if ($analytics->current_quantity >= $quantity) {
          $analytics->current_quantity -= $quantity;
          $analytics->total_stock_out += $quantity;
        } else {
          // Handle case when there's not enough stock to remove
          throw new \Exception("Not enough stock to perform stock-out.");
        }
        break;
    }

    // Calculate and update the turnover ratio, average daily stock-in/out, and stock-out days estimate
    $this->updateAnalyticsMetrics($analytics, $item, $quantity, $operation);

    // Save the analytics record after updates
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
    // Calculate average daily stock-in
    if ($analytics->total_stock_in_quantity > 0) {
      $analytics->avg_daily_stock_in = $analytics->total_stock_in_quantity / 30; // Assuming 30 days
    }

    // Calculate average daily stock-out
    if ($analytics->total_stock_out > 0) {
      $analytics->avg_daily_stock_out = $analytics->total_stock_out / 30; // Assuming 30 days
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