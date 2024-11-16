<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ApiItemController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $items = Item::all(); // Get all items
        return response()->json($items); // Return items as JSON
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:items,sku',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'type' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the image upload
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('item_images', 'public')
            : null;

        // Create the item
        $item = Item::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'cost' => $request->cost,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'type' => $request->type,
            'brand' => $request->brand,
            'image' => $imagePath,
        ]);

        // Log transaction for item creation
        Transaction::create([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'type' => 'created',
            'quantity' => $item->quantity,
            'unit_price' => $item->cost,
            'total_price' => $item->cost * $item->quantity,
            'date' => now(),
        ]);

        return response()->json([
            'message' => 'Item created successfully!',
            'item' => $item
        ], 201);
    }

    // Show the specified resource.
    public function show(Item $item)
    {
        return response()->json($item); // Return the item as JSON
    }

    // Update the specified resource in storage.
    public function update(Request $request, Item $item)
    {
        try {
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|unique:items,sku,' . $item->id,
                'cost' => 'nullable|numeric',
                'price' => 'nullable|numeric',
                'quantity' => 'nullable|integer',
                'type' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation for image upload
            ]);

            // Handle image upload if present
            $imagePath = $item->image; // Default image to the current one

            if ($request->hasFile('image')) {
                // Delete the old image if it exists in the storage
                if ($item->image && Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete($item->image);
                }

                // Store the new image and get its path
                $imagePath = $request->file('image')->store('item_images', 'public');
            }

            // Update the item data
            $item->update([
                'name' => $request->name,
                'sku' => $request->sku,
                'cost' => $request->cost,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'type' => $request->type,
                'brand' => $request->brand,
                'image' => $imagePath,
            ]);

            // Log the transaction for the update
            Transaction::create([
                'item_id' => $item->id,
                'item_name' => $item->name,
                'type' => 'edit',
                'quantity' => $item->quantity,
                'unit_price' => $item->cost,
                'total_price' => $item->cost * $item->quantity,
                'date' => now(),
            ]);

            return response()->json([
                'message' => 'Item updated successfully!',
                'item' => $item,
            ]);
        } catch (\Exception $e) {
            // Return a detailed error message for debugging
            return response()->json([
                'message' => 'Failed to update item.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Remove the specified resource from storage.
    public function destroy(Item $item)
    {
        // Delete the image if it exists
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        // Log transaction for item deletion
        Transaction::create([
            'item_id' => $item->id,
            'item_name' => $item->name,
            'type' => 'delete',
            'quantity' => 0,
            'unit_price' => $item->cost,
            'total_price' => 0,
            'date' => now(),
        ]);

        // Delete the item
        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully!'
        ]);
    }
}
