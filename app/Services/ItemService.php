<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class ItemService
{
  public function createItem($data)
  {
    // Handle the image upload
    $imagePath = isset($data['image']) ? $data['image']->store('item_images', 'public') : null;

    // Create item
    $item = Item::create(array_merge($data, ['image' => $imagePath]));

    // Log transaction
    Transaction::create([
      'item_id' => $item->id,
      'item_name' => $item->name,
      'type' => 'create',
      'quantity' => $item->quantity,
      'unit_price' => $item->cost,
      'total_price' => $item->cost * $item->quantity,
      'date' => now(),
    ]);

    return $item;
  }

  public function updateItem(Item $item, $data)
  {
    // Handle the image upload
    $imagePath = $item->image;
    if (isset($data['image'])) {
      if ($imagePath && Storage::disk('public')->exists($imagePath)) {
        Storage::disk('public')->delete($imagePath);
      }
      $imagePath = $data['image']->store('item_images', 'public');
    }

    $item->update(array_merge($data, ['image' => $imagePath]));

    // Log transaction
    Transaction::create([
      'item_id' => $item->id,
      'item_name' => $item->name,
      'type' => 'edit',
      'quantity' => $item->quantity,
      'unit_price' => $item->cost,
      'total_price' => $item->cost * $item->quantity,
      'date' => now(),
    ]);

    return $item;
  }

  public function deleteItem(Item $item)
  {
    if ($item->image && Storage::disk('public')->exists($item->image)) {
      Storage::disk('public')->delete($item->image);
    }

    Transaction::create([
      'item_id' => $item->id,
      'item_name' => $item->name,
      'type' => 'delete',
      'quantity' => 0,
      'unit_price' => $item->cost,
      'total_price' => 0,
      'date' => now(),
    ]);

    $item->delete();
  }
}
