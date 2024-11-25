<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Summary;
use Livewire\Component;
use App\Models\Analytics;
use App\Models\Transaction;
use Livewire\WithFileUploads;
use App\Services\AnalyticsService;
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
        if (auth()->user()->hasRole('super admin')) {
            $this->items = Item::all();
        } else {
            $this->team_id = $team_id ?? auth()->user()->team_id; // Initialize with the current user's team ID
            $this->items = Item::where('team_id', $this->team_id)->get(); // Filter items by team
        }

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
        // Validate the input
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

        // Handle image upload if applicable
        if (isset($this->newItem['image']) && $this->newItem['image']) {
            $this->newItem['image'] = $this->newItem['image']->store('item_images', 'public');
        }

        // Check if editing or creating
        if ($this->isEditing && $this->currentItem) {
            $item = Item::findOrFail($this->currentItem['id']); // Use findOrFail for safety
            $originalQuantity = $item->quantity;
            $quantityDifference = $this->newItem['quantity'] - $originalQuantity;

            // Delete old image if a new image is uploaded
            if (isset($this->newItem['image']) && $item->image) {
                Storage::disk('public')->delete($item->image);
            }

            // Update the item

            $item->update($this->newItem);

            // Log quantity adjustment as a transaction
            if ($quantityDifference != 0) {
                $this->logTransaction($item, 'adjusted', $quantityDifference);
            }
        } else {
            // Create a new item
            $this->newItem['team_id'] = auth()->user()->team_id;
            $item = Item::create($this->newItem);

            // Log the creation transaction
            $this->logTransaction($item, 'created', $item->quantity);
            $analyticsService = new AnalyticsService();
            $analyticsService->updateAllAnalytics($item, $item->quantity, 'created');
        }

        // Refresh items list and close modal
        $this->fetchItems();
        $this->closeModal();
    }

    /**
     * Log a transaction for an item.
     *
     * @param Item $item
     * @param string $type
     * @param int $quantityDifference
     */
    private function logTransaction($item, $type, $quantityDifference)
    {
        Transaction::create([
            'item_id' => $item->id,
            'team_id' => auth()->user()->team_id,
            'item_name' => $item->name,
            'type' => $type,
            'quantity' => $quantityDifference,
            'unit_price' => $item->cost,
            'total_price' => $item->cost * $quantityDifference,
            'date' => now(),
        ]);
    }

    public function deleteItem($itemId)
    {
        $teamId = auth()->user()->team_id;
        $item = Item::find($itemId);

        if ($item) {
            // If item has an image, delete it from storage
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            Transaction::create([
                'item_id' => $item->id,
                'team_id' => $teamId,
                'item_name' => $item->name,
                'type' => 'deleted',
                'quantity' => $item->quantity,
                'unit_price' => $item->cost,
                'total_price' => $item->cost * $item->quantity,
                'date' => now(),
            ]);

            Analytics::where('item_id', $itemId)->delete();
            Summary::where('item_id', $itemId)->delete();
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