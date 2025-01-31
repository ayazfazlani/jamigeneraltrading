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
    public $transactions = [];
    public $selectedItems = [];
    public $isModalOpen = false;
    public $search = '';
    public $dateRange = [
        'start' => '',
        'end' => ''
    ];
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
        $this->loadTransactions();
    }

    public function loadItems()
    {
        $teamId = Auth::user()->getCurrentTeamId();
        $this->items = Item::when(
            !Auth::user()->hasRole('super admin'),
            fn($q) => $q->where('team_id', $teamId)
        )
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function loadTransactions()
    {
        $teamId = Auth::user()->getCurrentTeamId();

        $this->transactions = Transaction::where('type', 'stock in')
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->when(
                $this->dateRange['start'] && $this->dateRange['end'],
                fn($q) => $q->whereBetween('date', [
                    $this->dateRange['start'],
                    $this->dateRange['end']
                ])
            )
            ->latest()
            ->get();
    }

    public function updatedSearch()
    {
        $this->loadItems();
    }

    public function updatedDateRange()
    {
        $this->loadTransactions();
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
            'newItem.image' => 'nullable|image|max:2048',
        ]);

        $teamId = Auth::user()->getCurrentTeamId();
        $imagePath = $this->newItem['image'] ? $this->newItem['image']->store('item_images', 'public') : null;

        DB::beginTransaction();
        try {
            $item = Item::create([
                'sku' => $this->newItem['sku'],
                'name' => $this->newItem['name'],
                'cost' => $this->newItem['cost'],
                'price' => $this->newItem['price'],
                'type' => $this->newItem['type'],
                'brand' => $this->newItem['brand'],
                'quantity' => $this->newItem['quantity'],
                'image' => $imagePath,
                'team_id' => $teamId,
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

            (new AnalyticsService())->updateAllAnalytics($item, $item->quantity, 'stock_in');

            DB::commit();
            session()->flash('message', 'Item added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error adding item: ' . $e->getMessage());
        }

        $this->reset('newItem');
        $this->loadItems();
        $this->isModalOpen = false;
    }

    public function handleStockIn()
    {
        DB::beginTransaction();
        $teamId = Auth::user()->getCurrentTeamId();
        try {
            foreach ($this->selectedItems as $item) {
                $itemModel = Item::find($item['id']);
                if ($itemModel) {
                    $itemModel->quantity += $item['quantity'];
                    $itemModel->save();

                    Transaction::create([
                        'item_id' => $itemModel->id,
                        'team_id' => $teamId,
                        'user_id' => Auth::id(),
                        'item_name' => $itemModel->name,
                        'type' => 'stock in',
                        'quantity' => $item['quantity'],
                        'unit_price' => $itemModel->cost,
                        'total_price' => $itemModel->cost * $item['quantity'],
                        'date' => now(),
                    ]);

                    (new AnalyticsService())->updateAllAnalytics($itemModel, $item['quantity'], 'stock_in');
                }
            }

            DB::commit();
            session()->flash('message', 'Stock-in completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error during stock-in: ' . $e->getMessage());
        }

        $this->reset('selectedItems');
        $this->loadItems();
        $this->loadTransactions();
    }

    public function render()
    {
        return view('livewire.stock-in');
    }
}
