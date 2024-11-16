<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetching analytics data for all items
        $analyticsData = Analytics::with('items')  // Assuming a relationship exists
            ->get();


        return $analyticsData;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }




    // custome functions 

    // Endpoint to get summary data (total inventory, stock in, stock out)
    public function getSummaryData(): JsonResponse
    {
        $totalInventory = Analytics::sum('current_quantity'); // Sum of current quantity as total inventory
        $stockIn = Analytics::sum('total_stock_in'); // Sum of total stock in
        $stockOut = Analytics::sum('total_stock_out'); // Sum of total stock out

        return response()->json([
            'totalInventory' => $totalInventory,
            'stockIn' => $stockIn,
            'stockOut' => $stockOut
        ]);
    }

    // Endpoint to get data for the total inventory chart
    public function getTotalInventoryData(): JsonResponse
    {
        $data = Analytics::select('created_at as date', 'current_quantity as value')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($data);
    }

    // Endpoint to get data for the stock-in chart
    public function getStockInData(): JsonResponse
    {
        $data = Analytics::select('created_at as date', 'total_stock_in as value')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($data);
    }

    // Endpoint to get data for the stock-out chart
    public function getStockOutData(): JsonResponse
    {
        $data = Analytics::select('created_at as date', 'total_stock_out as value')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($data);
    }
}
