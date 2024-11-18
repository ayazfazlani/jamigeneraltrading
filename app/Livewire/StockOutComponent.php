<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\StockOut;
use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;

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
        $this->items = Item::all();
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
