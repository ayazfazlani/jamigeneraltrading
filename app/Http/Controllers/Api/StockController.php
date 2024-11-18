<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class StockController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Handle stock in operation.
     */
    public function stockIn(Request $request)
    {
        $request->validate([
            'item_id' => 'nullable|required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $item = Item::findOrFail($request->item_id);
            $costPerUnit = $item->cost;

            // Record stock in details
            $stockIn = StockIn::create([
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'cost_per_unit' => $costPerUnit,
                'date' => now(),
            ]);

            // Update item quantity
            $item->increment('quantity', $request->quantity);

            // Log transaction
            Transaction::create([
                'item_id' => $item->id,
                'type' => 'stock_in',
                'item_name' => $item->name,
                'quantity' => $request->quantity,
                'unit_price' => $costPerUnit,
                'total_price' => $costPerUnit * $request->quantity,
                'date' => now(),
            ]);
            // Update analytics
            $this->analyticsService->updateAllAnalytics($item, $request->quantity, 'stock_in');

            DB::commit();

            return response()->json(['message' => 'Stock added successfully!', 'stockIn' => $stockIn]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to process stock in: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle stock out operation.
     */
    public function stockOut(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $item = Item::findOrFail($request->item_id);
            $costPerUnit = $item->cost;

            if ($item->quantity < $request->quantity) {
                return response()->json(['error' => 'Insufficient stock!'], 400);
            }

            // Record stock out details
            $stockOut = StockOut::create([
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'cost_per_unit' => $costPerUnit,
                'date' => now(),
            ]);

            // Update item quantity
            $item->decrement('quantity', $request->quantity);

            // Log transaction
            Transaction::create([
                'item_id' => $item->id,
                'type' => 'stock_out',
                'item_name' => $item->name,
                'quantity' => $request->quantity,
                'unit_price' => $costPerUnit,
                'total_price' => $costPerUnit * $request->quantity,
                'date' => now(),
            ]);
            // Update analytics
            $this->analyticsService->updateAllAnalytics($item, $request->quantity, 'stock_out');

            DB::commit();

            return response()->json(['message' => 'Stock removed successfully!', 'stockOut' => $stockOut]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to process stock out: ' . $e->getMessage()], 500);
        }
    }

    // Other methods remain the same...
}
