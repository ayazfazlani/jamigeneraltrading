<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\Item;
use Carbon\Carbon;

class AnalyticsService
{
  public function updateAnalyticsOnStockIn(Item $item, $quantity)
  {
    // Fetch or create analytics for the item
    $analytics = Analytics::firstOrCreate(['item_id' => $item->id]);

    // Update current quantity from the item
    $analytics->current_quantity = $item->quantity;

    // Increment total stock-in by the quantity added
    $analytics->total_stock_in += $quantity;

    // Recalculate average daily stock-in (this shouldn't reset avg daily stock-out)
    $analytics->avg_daily_stock_in = $this->calculateAverageDailyStockIn($analytics);

    // Recalculate turnover ratio
    $analytics->turnover_ratio = $this->calculateTurnoverRatio($analytics);

    // Save the analytics update
    $analytics->save();
  }

  public function updateAnalyticsOnStockOut(Item $item, $quantity)
  {
    // Fetch or create analytics for the item
    $analytics = Analytics::firstOrCreate(['item_id' => $item->id]);

    // Update the current quantity from the updated item model
    $analytics->current_quantity = $item->quantity;

    // Increase total stock-out
    $analytics->total_stock_out += $quantity;

    // Calculate the average daily stock-out using the total stock-out
    $analytics->avg_daily_stock_out = $this->calculateAverageDailyStockOut($analytics);

    // Calculate other metrics
    $analytics->stock_out_days_estimate = $this->calculateStockOutDaysEstimate($analytics);
    $analytics->turnover_ratio = $this->calculateTurnoverRatio($analytics);

    // Save analytics after updates
    $analytics->save();
  }

  // Calculate the average daily stock-in
  protected function calculateAverageDailyStockIn($analytics)
  {
    $daysActive = $this->getDaysActive($analytics->created_at);
    return $daysActive > 0 ? $analytics->total_stock_in / $daysActive : 0;
  }

  // Calculate the average daily stock-out
  protected function calculateAverageDailyStockOut($analytics)
  {
    $daysActive = $this->getDaysActive($analytics->created_at);
    return $daysActive > 0 ? $analytics->total_stock_out / $daysActive : 0;
  }

  // Calculate turnover ratio based on stock-out and average quantity
  protected function calculateTurnoverRatio($analytics)
  {
    if ($analytics->average_quantity > 0) {
      return $analytics->total_stock_out / $analytics->average_quantity;
    }
    return 0;
  }

  // Estimate the number of days before stock-out based on average daily stock-out
  protected function calculateStockOutDaysEstimate($analytics)
  {
    if ($analytics->avg_daily_stock_out > 0) {
      return $analytics->current_quantity / $analytics->avg_daily_stock_out;
    }
    return null; // null if avg_daily_stock_out is 0 to avoid division by zero
  }

  // Calculate the number of active days since the analytics was created
  protected function getDaysActive($startDate)
  {
    $start = Carbon::parse($startDate);
    $now = Carbon::now();
    return $start->diffInDays($now) ?: 1; // Ensure at least 1 day
  }
}
