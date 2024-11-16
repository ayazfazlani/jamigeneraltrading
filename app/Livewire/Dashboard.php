<?php

namespace App\Livewire;

use App\Models\Analytics;
use Livewire\Component;
use App\Models\Inventory;
use App\Models\StockIn;
use App\Models\StockOut;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $summary = [
        'totalInventory' => 0,
        'stockIn' => 0,
        'stockOut' => 0,
    ];
    public $totalInventoryData = [];
    public $stockInData = [];
    public $stockOutData = [];

    public function mount()
    {
        // Fetch the data from the models
        $this->fetchSummaryData();
        $this->fetchTotalInventoryData();
        $this->fetchStockInData();
        $this->fetchStockOutData();
    }

    public function fetchSummaryData()
    {
        // Calculate total quantities for inventory, stock in, and stock out
        $this->summary = [
            'totalInventory' => Analytics::sum('average_quantity'),
            'stockIn' => StockIn::sum('quantity'),
            'stockOut' => StockOut::sum('quantity'),
        ];
    }

    public function fetchTotalInventoryData()
    {
        // Fetch total inventory data for yesterday
        $this->totalInventoryData = Analytics::select('item_id', 'average_quantity')
            // ->whereDate('created_at', Carbon::yesterday())
            ->get();
    }

    public function fetchStockInData()
    {
        // Fetch stock-in data for yesterday
        $this->stockInData = StockIn::select('item_id', 'quantity')
            // ->whereDate('created_at', Carbon::yesterday())
            ->get();
    }

    public function fetchStockOutData()
    {
        // Fetch stock-out data for yesterday
        $this->stockOutData = StockOut::select('item_id', 'quantity')
            ->whereDate('created_at', Carbon::yesterday())
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard'); // This will use the dashboard blade view
    }
}
