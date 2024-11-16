<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithFileUploads;

class ItemList extends Component
{
    use WithFileUploads;

    public $items = [];
    public $newItem = [
        'sku' => '',
        'name' => '',
        'cost' => 0,
        'price' => 0,
        'type' => '',
        'brand' => '',
        'quantity' => 0,
    ];
    public $image;
    public $inStockOnly = false;
    public $isModalOpen = false;
    public $selectedItem = null;  // To store the selected item details

    public function mount()
    {
        $this->items = Item::all();
    }

    public function selectItem($itemId)
    {
        $this->selectedItem = Item::find($itemId);
    }

    public function toggleModal()
    {
        $this->isModalOpen = !$this->isModalOpen;
    }

    public function addItem()
    {
        $this->validate([
            'newItem.sku' => 'required|string|max:255',
            'newItem.name' => 'required|string|max:255',
            'newItem.cost' => 'required|numeric',
            'newItem.price' => 'required|numeric',
            'newItem.type' => 'required|string|max:255',
            'newItem.brand' => 'required|string|max:255',
            'newItem.quantity' => 'required|numeric',
            'image' => 'nullable|image|max:1024', // Optional image field
        ]);

        $item = new Item();
        $item->sku = $this->newItem['sku'];
        $item->name = $this->newItem['name'];
        $item->cost = $this->newItem['cost'];
        $item->price = $this->newItem['price'];
        $item->type = $this->newItem['type'];
        $item->brand = $this->newItem['brand'];
        $item->quantity = $this->newItem['quantity'];

        if ($this->image) {

            $item->image = $this->image->store('item_images', 'public');
        }

        $item->save();

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


        $this->items[] = $item;  // Add to the items list
        $this->resetNewItem();    // Reset form fields
        $this->toggleModal();     // Close modal
    }

    private function resetNewItem()
    {
        $this->newItem = [
            'sku' => '',
            'name' => '',
            'cost' => 0,
            'price' => 0,
            'type' => '',
            'brand' => '',
            'quantity' => 0,
        ];
        $this->image = null;
    }

    public function render()
    {
        return view('livewire.item-list');
    }
}
