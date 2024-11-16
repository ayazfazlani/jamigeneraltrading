<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class Adjust extends Component
{
    use WithFileUploads;

    public $items = [];
    public $selectedItems = [];
    public $isModalOpen = false;
    public $isEditing = false;
    public $currentItem = null;
    public $newItem = [
        'sku' => '',
        'name' => '',
        'cost' => '',
        'price' => '',
        'type' => '',
        'brand' => '',
        'image' => null,
        'quantity' => 0,
    ];
    public $loading = false;

    public function mount()
    {
        $this->fetchItems();
    }

    public function fetchItems()
    {
        $this->loading = true;
        $this->items = Item::all();
        $this->loading = false;
    }

    public function openModal($itemId = null)
    {
        if ($itemId) {
            $item = Item::find($itemId);
            $this->currentItem = $item;
            $this->newItem = $item->toArray();
            $this->isEditing = true;
        } else {
            $this->resetNewItem();
            $this->isEditing = false;
        }
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->currentItem = null;
        $this->resetNewItem();
    }

    public function handleInputChange($name, $value)
    {
        $this->newItem[$name] = $value;
    }

    public function handleImageChange($file)
    {
        if ($file) {
            // Delete old image if exists (on update)
            if ($this->isEditing && $this->currentItem && $this->currentItem['image']) {
                // Delete the old image from storage
                Storage::disk('public')->delete($this->currentItem['image']);
            }

            // Store the new image in 'item_images' folder
            $this->newItem['image'] = $file->store('item_images', 'public');
        }
    }

    public function saveItem()
    {
        $validated = $this->validate([
            'newItem.sku' => 'required',
            'newItem.name' => 'required',
            'newItem.cost' => 'required|numeric',
            'newItem.price' => 'required|numeric',
            'newItem.type' => 'required',
            'newItem.brand' => 'required',
            'newItem.quantity' => 'required|numeric',
            'newItem.image' => 'nullable|image|max:1024',
        ]);

        if ($this->isEditing && $this->currentItem) {
            $item = Item::find($this->currentItem['id']);
            $originalQuantity = $item->quantity;
            $quantityDifference = $this->newItem['quantity'] - $originalQuantity;

            if ($quantityDifference != 0) {
                Transaction::create([
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'type' => 'adjusted',
                    'quantity' => $quantityDifference,
                    'unit_price' => $item->cost,
                    'total_price' => $item->cost * $quantityDifference,
                    'date' => now(),
                ]);
            }

            // Update item data
            $item->update($this->newItem);
        } else {
            // Create a new item
            $item = Item::create($this->newItem);

            Transaction::create([
                'item_id' => $item->id,
                'item_name' => $item->name,
                'type' => 'created',
                'quantity' => $item->quantity,
                'unit_price' => $item->cost,
                'total_price' => $item->cost * $item->quantity,
                'date' => now(),
            ]);
        }

        $this->fetchItems();
        $this->closeModal();
    }

    public function deleteItem($itemId)
    {
        $item = Item::find($itemId);

        if ($item) {
            // If item has an image, delete it from storage
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            Transaction::create([
                'item_id' => $item->id,
                'item_name' => $item->name,
                'type' => 'deleted',
                'quantity' => $item->quantity,
                'unit_price' => $item->cost,
                'total_price' => $item->cost * $item->quantity,
                'date' => now(),
            ]);

            // Delete the item
            $item->delete();
            $this->fetchItems();
        }
    }

    private function resetNewItem()
    {
        $this->newItem = [
            'sku' => '',
            'name' => '',
            'cost' => '',
            'price' => '',
            'type' => '',
            'brand' => '',
            'image' => null,
            'quantity' => 0,
        ];
    }

    public function render()
    {
        return view('livewire.adjust');
    }
}
