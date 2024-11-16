<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class StockInComponent extends Component
{
    use WithFileUploads;  // For handling file uploads

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
        'quantity' => 0,
        'image' => null, // To hold the uploaded image
    ];

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        // Fetch items from the database
        $this->items = Item::all()->toArray();
    }

    public function toggleItemSelection($itemId)
    {
        $key = array_search($itemId, array_column($this->selectedItems, 'id'));

        if ($key === false) {
            // Add selected item with an initial quantity
            $item = Item::find($itemId)->toArray();
            $item['quantity'] = 1;  // Default quantity for stock-in
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

    public function addItem()
    {
        // Handle the logic for adding a new item
        $this->validate([
            'newItem.sku' => 'required|unique:items,sku',
            'newItem.name' => 'required',
            'newItem.cost' => 'required|numeric',
            'newItem.price' => 'required|numeric',
            'newItem.type' => 'required',
            'newItem.brand' => 'required',
            'newItem.quantity' => 'required|numeric',
            'newItem.image' => 'nullable|image|max:1024',  // Handle image validation
        ]);

        $imagePath = null;
        if ($this->newItem['image']) {
            $imagePath = $this->newItem['image']->store('item_images', 'public');
        }

        $item = Item::create([
            'sku' => $this->newItem['sku'],
            'name' => $this->newItem['name'],
            'cost' => $this->newItem['cost'],
            'price' => $this->newItem['price'],
            'type' => $this->newItem['type'],
            'brand' => $this->newItem['brand'],
            'quantity' => $this->newItem['quantity'],
            'image' => $imagePath,  // Save image path in database
        ]);

        $itemId = $item->id;
        Transaction::create([
            'item_id' => $itemId,
            'item_name' => $item->name,
            'type' => 'created',
            'quantity' => $item->quantity,
            'unit_price' => $item->cost,
            'total_price' => $item->cost * $item->quantity,
            'date' => now(),
        ]);

        session()->flash('message', 'Item added successfully.');
        $this->loadItems();
        $this->isModalOpen = false;  // Close modal after adding item
    }

    public function handleStockIn()
    {
        DB::beginTransaction();

        try {
            foreach ($this->selectedItems as $item) {
                $itemModel = Item::find($item['id']);
                if ($itemModel) {
                    // Update the item quantity
                    $itemModel->quantity += $item['quantity'];
                    $itemModel->save();

                    // Record stock-in details using the fully qualified model name
                    StockIn::create([
                        'item_id' => $itemModel->id,
                        'quantity' => $item['quantity'],
                        'cost_per_unit' => $itemModel->cost,
                        'date' => now(),
                    ]);

                    // Log the transaction
                    Transaction::create([
                        'item_id' => $itemModel->id,
                        'type' => 'stock in',
                        'item_name' => $itemModel->name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $itemModel->cost,
                        'total_price' => $itemModel->cost * $item['quantity'],
                        'date' => now(),
                    ]);
                }
            }

            DB::commit();
            session()->flash('message', 'Stock-in action completed successfully.');

            // Reset selected items
            $this->selectedItems = [];
            $this->loadItems();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to process stock-in: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.stock-in');
    }
}
