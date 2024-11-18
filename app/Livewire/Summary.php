<?php

namespace App\Livewire;

use App\Models\Analytics;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class Summary extends Component
{
    public $dateRange = ''; // Date range in 'YYYY-MM-DD to YYYY-MM-DD'
    public $search = ''; // Search term for filtering
    public $reports = []; // Filtered reports

    // Fetch all reports on mount
    public function mount()
    {
        $this->fetchReports();
    }

    // Fetch filtered reports
    public function fetchReports()
    {
        $query = DB::table('analytics')
            ->select('id', 'item_name', 'total_stock_in', 'total_stock_out', 'current_quantity', 'inventory_assets');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where('item_name', 'like', '%' . $this->search . '%');
        }

        // Apply date range filter
        if (!empty($this->dateRange)) {
            $dates = explode(' to ', $this->dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }

        $this->reports = $query->get();
    }

    // Filter reports dynamically
    public function filterReports()
    {
        $this->fetchReports();
    }

    // Export filtered reports to Excel
    public function exportExcel()
    {
        return Excel::download(new ReportsExport($this->reports), 'reports.xlsx');
    }

    public function render()
    {
        return view('livewire.summary');
    }
}
