<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\StockOut;
use App\Models\Transaction;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOutComponent extends Component
{
    public $items = [];
    public $selectedItems = [];
    public $isModalOpen = false;

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
            if (!$teamId) {
                $teamId = Auth::user()->team_id;
            }
            // dump($teamId);
            // if (!$teamId) {
            //     // Handle the case where no team is active
            //     session()->flash('error', 'No active team selected.');
            //     $this->items = [];
            //     return;
            // }
            $this->items = Item::where('team_id', $teamId)->get();
        }
    }

    public function toggleItemSelection($itemId)
    {
        $key = array_search($itemId, array_column($this->selectedItems, 'id'));

        if ($key === false) {
            $item = Item::find($itemId)->toArray();
            $item['quantity'] = 1;  // Default quantity for stock-out
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

    public function handleStockOut()
    {
        DB::beginTransaction();

        try {
            foreach ($this->selectedItems as $item) {
                $itemModel = Item::find($item['id']);
                if ($itemModel) {
                    // Ensure the stock is available for removal
                    if ($itemModel->quantity >= $item['quantity']) {
                        // Update the item quantity
                        $itemModel->quantity -= $item['quantity'];
                        $itemModel->save();

                        // Log the transaction
                        Transaction::create([
                            'item_id' => $itemModel->id,
                            'team_id' => session('current_team_id'),
                            'user_id' => Auth::user()->id,
                            'item_name' => $itemModel->name,
                            'type' => 'stock out',
                            'quantity' => $item['quantity'],
                            'unit_price' => $itemModel->cost,
                            'total_price' => $itemModel->cost * $item['quantity'],
                            'date' => now(),
                        ]);

                        // Update Analytics after stock-out
                        $analyticsService = new AnalyticsService();
                        $analyticsService->updateAllAnalytics($itemModel, $item['quantity'], 'stock_out');
                    }
                    DB::commit();
                    session()->flash('message', 'Stock-out completed successfully');
                }
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
        return view('livewire.stock-out');
    }
}
