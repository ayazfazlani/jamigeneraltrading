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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

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

    public function mount()
    {
        // Fetch items based on the user's role
        $this->fetchItems();
    }

    public function fetchItems()
    {
        if (Auth::user()->hasRole('super admin')) {
            // Super admin can see all items
            $this->items = Item::all();
        } else {
            // Regular users see items based on their current team
            // $this->items = Item::where('team_id', Auth::user()->team_id)->get();
            // Fetch items for all teams the user belongs to
            // Regular users see items based on their teams

            // $teamIds = Auth::user()->teams()->pluck('teams.id'); // Specify the table name

            $teamId = (int) session('current_team_id');
            // dump($teamId);
            if (!$teamId) {
                $teamId = Auth::user()->team_id;
            }
            $this->items = Item::where('team_id', $teamId)->get();
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
            'image' => 'nullable|image|max:2048',
        ]);

        $teamId = (int) session('current_team_id');
        // dump($teamId);
        if (!$teamId) {
            $teamId = Auth::user()->team_id;
        }
        // Create the new item
        $item = Item::create([
            'team_id' => $teamId,
            'user_id' => Auth::user()->id,
            'sku' => $this->newItem['sku'],
            'name' => $this->newItem['name'],
            'cost' => $this->newItem['cost'],
            'price' => $this->newItem['price'],
            'type' => $this->newItem['type'],
            'brand' => $this->newItem['brand'],
            'quantity' => $this->newItem['quantity'],
        ]);

        // Handle image upload (if any)
        if ($this->image) {
            $item->image = $this->image->store('item_images', 'public');
            $item->save();
        }

        // Log the transaction for this item creation
        Transaction::create([
            'item_id' => $item->id,
            'team_id' => $teamId,
            'user_id' => Auth::user()->id,
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
