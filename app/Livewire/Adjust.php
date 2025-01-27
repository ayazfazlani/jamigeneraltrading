<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Summary;
use Livewire\Component;
use App\Models\Analytics;
use App\Models\Transaction;
use Livewire\WithFileUploads;
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
        $this->loading = true;
        if (Auth::user()->hasRole('super admin')) {
            $this->items = Item::all();
        } else {
            $teamId = session('current_team_id');
            if (!$teamId) {
                $teamId = Auth::user()->team_id;
            }
            $this->items = Item::where('team_id', $teamId)->get();
        }
        $this->loading = false;
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
        $validated = $this->validate($this->getValidationRules());
        $teamId = session('current_team_id');
        if (!$teamId) {
            $teamId = Auth::user()->team_id;
        }

        if ($this->isEditing && $this->currentItem) {
            $item = Item::findOrFail($this->currentItem['id']);
            $originalQuantity = $item->quantity;
            $quantityDifference = $this->newItem['quantity'] - $originalQuantity;

            $this->newItem['image'] = $this->handleImageUpload(
                $this->newItem['image'] ?? null,
                $item->image
            );

            $item->update($this->newItem);

            if ($quantityDifference != 0) {
                $this->logTransaction($item, 'adjusted', $quantityDifference);
            }
        } else {
            $this->newItem['image'] = $this->handleImageUpload($this->newItem['image']);
            $this->newItem['team_id'] = $teamId;

            $item = Item::create($this->newItem);

            $this->logTransaction($item, 'created', $item->quantity);

            $analyticsService = new AnalyticsService();
            $analyticsService->updateAllAnalytics($item, $item->quantity, 'created');
        }

        $this->fetchItems();
        $this->closeModal();
    }

    private function logTransaction($item, $type, $quantityDifference)
    {
        $teamId = session('current_team_id');
        if (!$teamId) {
            $teamId = Auth::user()->team_id;
        }
        Transaction::create([
            'item_id' => $item->id,
            'user_id' => Auth::user()->id,
            'team_id' => $teamId,
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
        $teamId = session('current_team_id');
        // dd($teamId);
        if (!$teamId) {
            $teamId = Auth::user()->team_id;
        }
        try {
            $item = Item::findOrFail($itemId);

            if (!Auth::user()->teams->contains('id', $teamId)) {
                abort(403, 'Unauthorized');
            }

            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }

            $this->logTransaction($item, 'deleted', $item->quantity);

            Analytics::where('item_id', $itemId)->delete();
            Summary::where('item_id', $itemId)->delete();
            $item->delete();

            $this->fetchItems();
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to delete the item: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.adjust', [
            'items' => $this->items,
        ]);
    }
}
