<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\StockIn;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithFileUploads;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockInComponent extends Component
{
    use WithFileUploads;

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
        'image' => null,
    ];

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        // if (auth()->user()->hasRole('super admin')) {
        //     $this->items = Item::all();
        // } else {
        //     $this->team_id = $team_id ?? auth()->user()->team_id; // Initialize with the current user's team ID
        //     $this->items = Item::where('team_id', $this->team_id)->get(); // Filter items by team
        // }
        if (Auth::user()->hasRole('super admin')) {
            // Super admin can see all items
            $this->items = Item::all();
        } else {
            $teamId = (int) session('current_team_id');
            // dump($teamId);
            if (!$teamId) {
                // Handle the case where no team is active
                session()->flash('error', 'No active team selected.');
                $this->items = [];
                return;
            }
            $this->items = Item::where('team_id', $teamId)->get();
        }
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
        $this->validate([
            'newItem.sku' => 'required|string|unique:items,sku',
            'newItem.name' => 'required|string',
            'newItem.cost' => 'required|numeric|min:0',
            'newItem.price' => 'required|numeric|min:0',
            'newItem.type' => 'required|string',
            'newItem.brand' => 'required|string',
            'newItem.quantity' => 'required|integer|min:0',
            'newItem.image' => 'nullable|image|max:2048', // Max file size: 2MB
        ]);

        $imagePath = null;
        if ($this->newItem['image']) {
            $imagePath = $this->newItem['image']->store('item_images', 'public');
        }

        // Create the new item
        $item = Item::create([
            'sku' => $this->newItem['sku'],
            'name' => $this->newItem['name'],
            'cost' => $this->newItem['cost'],
            'price' => $this->newItem['price'],
            'type' => $this->newItem['type'],
            'brand' => $this->newItem['brand'],
            'quantity' => $this->newItem['quantity'],
            'image' => $imagePath,
            'team_id' => session('current_team_id'),
        ]);

        // Create a transaction record for the new item
        Transaction::create([
            'item_id' => $item->id,
            'team_id' => session('current_team_id'),
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

        // Reset the form fields
        $this->newItem = [
            'sku' => '',
            'name' => '',
            'cost' => '',
            'price' => '',
            'type' => '',
            'brand' => '',
            'quantity' => 0,
            'image' => null,
        ];

        // Reload items and close the modal
        $this->loadItems();
        $this->isModalOpen = false;

        // Display success message
        session()->flash('message', 'Item added successfully!');
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

                    // Log the transaction
                    Transaction::create([
                        'item_id' => $itemModel->id,
                        'team_id' => auth()->user()->team_id,
                        'item_name' => $itemModel->name,
                        'type' => 'stock in',
                        'quantity' => $item['quantity'],
                        'unit_price' => $itemModel->cost,
                        'total_price' => $itemModel->cost * $item['quantity'],
                        'date' => now(),
                    ]);

                    // Update Analytics after stock-in
                    $analyticsService = new AnalyticsService();
                    $analyticsService->updateAllAnalytics($itemModel, $item['quantity'], 'stock_in');
                }
                DB::commit();
                session()->flash('message', 'Stock-in completed successfully');
            }


            $this->loadItems();
            $this->selectedItems = [];  // Clear selected items
            $this->toggleModal();  // Close modal
        } catch (\Exception $e) {
            DB::rollBack();
            // session()->flash('error', 'Error occurred: ' . $e->getMessage());
            session()->flash('error', 'Select item to stock out!');
        }
    }

    public function render()
    {
        return view('livewire.stock-in');
    }
}