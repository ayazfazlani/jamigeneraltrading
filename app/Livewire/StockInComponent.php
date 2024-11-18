<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;

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
        $this->items = Item::all()->toArray();
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
