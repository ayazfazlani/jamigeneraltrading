<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\User;
use Livewire\Component;
use App\Models\Transaction;
use App\Imports\ItemsImport;
use Livewire\WithFileUploads;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
    public $search = '';
    public $inStockOnly = false;
    public $isModalOpen = false;
    public $selectedItem = null;
    public $isImportModalOpen = false;
    public $importFile;

    public function mount()
    {
        $this->fetchItems();
    }

    public function fetchItems()
    {
        $teamId = Auth::user()->getCurrentTeamId();

        $query = Item::query()
            ->when($teamId, function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%')
                        ->orWhere('brand', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->inStockOnly, function ($query) {
                $query->where('quantity', '>', 0);
            });

        $this->items = $query->get();
    }

    public function updatedSearch()
    {
        $this->fetchItems();
    }

    public function updatedInStockOnly()
    {
        $this->fetchItems();
    }

    public function toggleImportModal()
    {
        $this->isImportModalOpen = !$this->isImportModalOpen;
    }

    public function importItems()
    {
        $this->validate(['importFile' => 'required|mimes:xlsx,csv']);
        Excel::import(new ItemsImport, $this->importFile->getRealPath());
        $this->fetchItems();
        $this->toggleImportModal();
    }

    public function selectItem($itemId)
    {
        $this->selectedItem = Item::find($itemId);
    }

    public function toggleModal()
    {
        $this->isModalOpen = !$this->isModalOpen;
        if (!$this->isModalOpen) $this->resetNewItem();
    }

    public function addItem()
    {
        $this->validate([
            'newItem.sku' => 'required|string|max:255',
            'newItem.name' => 'required|string|max:255',
            'newItem.cost' => 'required|numeric|min:0',
            'newItem.price' => 'required|numeric|min:0',
            'newItem.type' => 'required|string|max:255',
            'newItem.brand' => 'required|string|max:255',
            'newItem.quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $teamId = Auth::user()->getCurrentTeamId();

        $item = Item::create([
            'team_id' => $teamId,
            'user_id' => Auth::id(),
            ...$this->newItem,
            'image' => $this->image ? $this->image->store('item_images', 'public') : null
        ]);

        Transaction::create([
            'item_id' => $item->id,
            'team_id' => $teamId,
            'user_id' => Auth::id(),
            'item_name' => $item->name,
            'type' => 'created',
            'quantity' => $item->quantity,
            'unit_price' => $item->cost,
            'total_price' => $item->cost * $item->quantity,
            'date' => now(),
        ]);

        (new AnalyticsService())->updateAllAnalytics($item, $item->quantity, 'created');

        $this->fetchItems();
        $this->toggleModal();
        session()->flash('success', 'Item added successfully!');
    }

    private function resetNewItem()
    {
        $this->reset('newItem', 'image');
    }

    public function render()
    {
        return view('livewire.item-list');
    }
}
