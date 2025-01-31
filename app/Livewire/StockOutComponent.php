<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\Transaction;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOutComponent extends Component
{
    public $items = [];
    public $transactions = [];
    public $selectedItems = [];
    public $search = '';
    public $dateRange = [
        'start' => '',
        'end' => ''
    ];

    protected $listeners = ['refreshComponent' => '$refresh'];

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

        $this->transactions = Transaction::where('type', 'stock out')
            ->when($teamId, fn($q) => $q->where('team_id', $teamId))
            ->when(
                $this->dateRange['start'] && $this->dateRange['end'],
                fn($q) => $q->whereBetween('created_at', [
                    $this->dateRange['start'] . ' 00:00:00',
                    $this->dateRange['end'] . ' 23:59:59'
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
            $item = Item::findOrFail($itemId)->toArray();
            $item['quantity'] = 1;
            $this->selectedItems[] = $item;
        } else {
            unset($this->selectedItems[$key]);
            $this->selectedItems = array_values($this->selectedItems);
        }
    }

    public function handleStockOut()
    {
        $this->validate([
            'selectedItems.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $itemId = $this->selectedItems[$index]['id'];
                    $item = Item::find($itemId);

                    if (!$item) {
                        $fail("Item not found");
                        return;
                    }

                    if ($value > $item->quantity) {
                        $fail("Quantity for {$item->name} exceeds available stock ({$item->quantity})");
                    }
                }
            ]
        ]);

        DB::beginTransaction();
        $teamId = Auth::user()->getCurrentTeamId();

        try {
            foreach ($this->selectedItems as $item) {
                $itemModel = Item::lockForUpdate()->find($item['id']);

                if ($itemModel) {
                    if ($itemModel->quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$itemModel->name}");
                    }

                    $itemModel->quantity -= $item['quantity'];
                    $itemModel->save();

                    Transaction::create([
                        'item_id' => $itemModel->id,
                        'team_id' => $teamId,
                        'user_id' => Auth::id(),
                        'item_name' => $itemModel->name,
                        'type' => 'stock out',
                        'quantity' => $item['quantity'],
                        'unit_price' => $itemModel->cost,
                        'total_price' => $itemModel->cost * $item['quantity'],
                        'created_at' => now(),
                    ]);

                    (new AnalyticsService())->updateAllAnalytics($itemModel, $item['quantity'], 'stock_out');
                }
            }

            DB::commit();
            // $this->dispatch('refreshComponent');
            session()->flash('message', 'Stock-out completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }

        $this->reset('selectedItems');
        $this->loadItems();
        $this->loadTransactions();
    }

    public function render()
    {
        return view('livewire.stock-out');
    }
}
