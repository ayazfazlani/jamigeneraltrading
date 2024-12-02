<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\User;
use Livewire\Component;
use App\Models\Transaction;
use App\Imports\ItemsImport;
use Livewire\WithFileUploads;
use App\Services\AnalyticsService;
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
    public $inStockOnly = false;
    public $isModalOpen = false;
    public $selectedItem = null; // To store the selected item details
    public $team_id = null;


    public $isImportModalOpen = false;
    public $importFile;

    public function toggleImportModal()
    {
        $this->isImportModalOpen = !$this->isImportModalOpen;
    }

    public function importItems()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new ItemsImport, $this->importFile->getRealPath());

        // Refresh the items list after import
        $this->items = Item::where('team_id', $this->team_id)->get();

        // Reset the file input and close the modal
        $this->importFile = null;
        $this->toggleImportModal();
    }

    public function mount($team_id = null)
    {
        if (auth()->user()->hasRole('super admin')) {
            $this->items = Item::get();
        } else {
            $this->team_id = $team_id ?? auth()->user()->team_id; // Initialize with the current user's team ID
            $this->items = Item::where('team_id', $this->team_id)->get(); // Filter items by team
        }
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
            'image' => 'nullable|image|max:2048', // Optional image field
        ]);

        // Create the new item
        $item = new Item();
        $item->team_id = $this->team_id;
        $item->sku = $this->newItem['sku'];
        $item->name = $this->newItem['name'];
        $item->cost = $this->newItem['cost'];
        $item->price = $this->newItem['price'];
        $item->type = $this->newItem['type'];
        $item->brand = $this->newItem['brand'];
        $item->quantity = $this->newItem['quantity'];
        // $item->image = $this->newItem['image'];

        // Handle image upload (if any)
        if ($this->image) {
            $item->image = $this->image->store('item_images', 'public');
            // dd($item->image);
        }

        // Save the item to the database
        $item->save();

        // Log the transaction for this item creation
        Transaction::create([
            'item_id' => $item->id,
            'team_id' => $item->team_id,
            'item_name' => $item->name,
            'type' => 'created',
            'quantity' => $item->quantity,
            'unit_price' => $item->cost,
            'total_price' => $item->cost * $item->quantity,
            'date' => now(),
        ]);

        // Update Analytics after item creation
        $analyticsService = new AnalyticsService();
        $analyticsService->updateAllAnalytics($item, $item->quantity, 'created');

        // Add the newly created item to the list of items
        $this->items[] = $item;

        // Reset the new item form fields and close the modal
        $this->resetNewItem();
        $this->toggleModal();
    }

    // Reset form data after item is added
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
