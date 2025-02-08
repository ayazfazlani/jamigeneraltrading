<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Summary;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Analytics;
use App\Models\Transaction;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Adjust extends Component
{
    use WithFileUploads;

    public $items = [];
    public $selectedItems = [];
    public $isModalOpen = false;
    public $isEditing = false;
    public $currentItem = null;
    public $newItem = [];
    public $loading = false;
    public $search = '';
    public $dateRange = ['start' => '', 'end' => ''];

    public function mount()
    {
        $this->resetNewItem();
        $this->fetchItems();
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

    public function fetchItems()
    {
        $teamId = Auth::user()->getCurrentTeamId();
        $this->loading = true;

        $query = Item::query()->when(!Auth::user()->hasRole('super admin'), fn($q) => $q->where('team_id', $teamId));

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }

        if ($this->dateRange['start'] && $this->dateRange['end']) {
            $query->whereBetween('created_at', [$this->dateRange['start'], $this->dateRange['end']]);
        }

        $this->items = $query->get();
        $this->loading = false;
    }

    public function updatedSearch()
    {
        $this->fetchItems();
    }
    public function updatedDateRange()
    {
        $this->fetchItems();
    }

    public function openModal($itemId = null)
    {
        if ($itemId) {
            $item = Item::findOrFail($itemId);
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

    private function getValidationRules()
    {
        return [
            'newItem.sku' => 'nullable|string|max:255',
            'newItem.name' => 'required|string|max:255',
            'newItem.cost' => 'nullable|numeric|min:0',
            'newItem.price' => 'nullable|numeric|min:0',
            'newItem.type' => 'nullable|string|max:255',
            'newItem.brand' => 'nullable|string|max:255',
            'newItem.quantity' => 'required|numeric|min:0',
            'newItem.image' => 'nullable|image|max:3072',
        ];
    }

    private function handleImageUpload($image, $oldImage = null)
    {
        if ($image) {
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            return $image->store('item_images', 'public');
        }
        return $oldImage;
    }

    public function saveItem()
    {
        $this->validate($this->getValidationRules());
        $teamId = Auth::user()->getCurrentTeamId();

        if ($this->isEditing && $this->currentItem) {
            $item = Item::findOrFail($this->currentItem['id']);
            $originalQuantity = $item->quantity;
            $quantityDifference = $this->newItem['quantity'] - $originalQuantity;

            $this->newItem['image'] = $this->handleImageUpload($this->newItem['image'] ?? null, $item->image);
            $item->update($this->newItem);

            if ($quantityDifference != 0) {
                $this->logTransaction($item, 'adjusted', $quantityDifference);
                (new AnalyticsService())->updateAllAnalytics($item, $item->quantity, 'update');
            }
            session()->flash('success', 'Item updated successfully!');
        } else {
            $this->newItem['image'] = $this->handleImageUpload($this->newItem['image']);
            $this->newItem['team_id'] = $teamId;

            $item = Item::create($this->newItem);
            $this->logTransaction($item, 'created', $item->quantity);
            (new AnalyticsService())->updateAllAnalytics($item, $item->quantity, 'created');
            session()->flash('success', 'Item added successfully!');
        }

        $this->fetchItems();
        $this->closeModal();
    }

    private function logTransaction($item, $type, $quantityDifference)
    {
        Transaction::create([
            'item_id' => $item->id,
            'user_id' => Auth::user()->id,
            'team_id' => Auth::user()->getCurrentTeamId(),
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
        try {
            $item = Item::findOrFail($itemId);
            if ($item->image) Storage::disk('public')->delete($item->image);
            $this->logTransaction($item, 'deleted', $item->quantity);
            Analytics::where('item_id', $itemId)->delete();
            Summary::where('item_id', $itemId)->delete();
            $item->delete();
            session()->flash('success', 'Item deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to delete the item: ' . $e->getMessage());
        }
        $this->fetchItems();
    }

    public function render()
    {
        return view('livewire.adjust', ['items' => $this->items]);
    }
}
