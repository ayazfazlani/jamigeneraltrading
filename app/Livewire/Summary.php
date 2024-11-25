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
        // Start the base query for fetching analytics reports
        $query = DB::table('analytics')
            ->select('id', 'item_name', 'total_stock_in', 'total_stock_out', 'current_quantity', 'inventory_assets');

        // Apply role-based filtering
        if (auth()->check()) {
            if (auth()->user()->hasRole('super admin')) {
                // Super admin sees all data
                // No team filter is applied for super admins
            } else if (auth()->user()->hasRole('team admin')) {
                // Team admin sees only their team's data
                $query->where('team_id', auth()->user()->team_id);
            }
        } else {
            // If the user is not authenticated, return empty reports
            $this->reports = collect();
            return;
        }

        // Apply search filter if provided
        if (!empty($this->search)) {
            $query->where('item_name', 'like', '%' . $this->search . '%');
        }

        // Apply date range filter if provided
        if (!empty($this->dateRange)) {
            $dates = explode(' to ', $this->dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }

        // Execute the query and fetch the filtered reports
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
