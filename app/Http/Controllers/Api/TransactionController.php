<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    // Get all transactions
    public function index()
    {
        $transactions = Transaction::with(['item', 'stockIn', 'stockOut'])->get();
        return response()->json($transactions);
    }

    // Get transactions for a specific item
    public function show($itemId)
    {
        $transactions = Transaction::where('item_id', $itemId)
            ->with(['stockIn', 'stockOut'])
            ->get();
        return response()->json($transactions);
    }
}
