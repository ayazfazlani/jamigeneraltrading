<?php

namespace App\Livewire;

use App\Models\Analytics;
use Livewire\Component;

class Dashboard extends Component
{
    public $summary = [];
    public $totalInventoryData = [];

    public function mount()
    {
        $this->fetchSummary();
        $this->fetchTotalInventoryData();
    }

    public function fetchSummary()
    {
        // Fetch summary data (total inventory, stock in, stock out)
        $this->summary = [
            'totalInventory' => Analytics::sum('current_quantity'),
            'stockIn' => Analytics::sum('total_stock_in'),
            'stockOut' => Analytics::sum('total_stock_out'),
        ];
    }

    public function fetchTotalInventoryData()
    {
        // Fetch data for the total inventory chart
        $this->totalInventoryData = Analytics::select('item_id', 'current_quantity')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item_id,
                    'quantity' => $item->current_quantity,
                ];
            });
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'summary' => $this->summary,
            'totalInventoryData' => $this->totalInventoryData,
        ]);
    }
}
