<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class StockOutComponent extends Component
{
    public $items = [];
    public $selectedItems = [];
    public $isModalOpen = false;
    public $newItem = [
        'sku' => '',
        'name' => '',
        'cost' => '',
        'price' => '',
        'type' => '',
        'brand' => '',
    ];

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        // Fetch items directly from the database
        $this->items = Item::all()->toArray();
    }

    public function toggleItemSelection($itemId)
    {
        $key = array_search($itemId, array_column($this->selectedItems, 'id'));

        if ($key === false) {
            // Find item by ID
            $item = Item::find($itemId)->toArray();
            $item['quantity'] = 1;  // Default quantity
            $this->selectedItems[] = $item;
        } else {
            unset($this->selectedItems[$key]);
            $this->selectedItems = array_values($this->selectedItems);
        }
    }

    public function updateQuantity($itemId, $quantity)
    {
        foreach ($this->selectedItems as &$item) {
            if ($item['id'] == $itemId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }

    public function handleStockOut()
    {
        DB::beginTransaction();

        try {
            foreach ($this->selectedItems as $item) {
                $itemModel = Item::find($item['id']);
                if ($itemModel) {
                    // Deduct the stock quantity
                    $itemModel->quantity -= $item['quantity'];
                    $itemModel->save();

                    // Record the stock-out transaction
                    Transaction::create([
                        'item_id' => $itemModel->id,
                        'type' => 'stock out',
                        'item_name' => $itemModel->name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $itemModel->cost,
                        'total_price' => $itemModel->cost * $item['quantity'],
                        'date' => now(),
                    ]);
                }
            }

            DB::commit();
            session()->flash('message', 'Stock out action completed successfully.');

            // Reset selected items
            $this->selectedItems = [];
            $this->loadItems();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to process stock-out: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.stock-out');
    }
}
