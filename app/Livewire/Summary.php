<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Analytics;
use App\Exports\ReportsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class Summary extends Component
{
    public $dateRange = ''; // Date range in 'YYYY-MM-DD to YYYY-MM-DD'
    public $search = ''; // Search term for filtering
    public $reports = []; // Filtered reports

    // Lifecycle hook: Fetch all reports on component mount
    public function mount()
    {
        $this->fetchReports();
    }

    /**
     * Fetch filtered reports from the database.
     */
    public function fetchReports()
    {
        $query = Analytics::query()
            ->select('id', 'item_name', 'total_stock_in', 'total_stock_out', 'current_quantity', 'inventory_assets', 'team_id', 'created_at');

        // Check user role and apply team-based filtering
        if (auth()->check()) {
            if (auth()->user()->hasRole('super admin')) {
                // Super admin sees all reports (no additional filters applied)
            } else {
                // Other roles: Filter by team ID
                $currentTeamId = session('current_team_id');

                if (!$currentTeamId) {
                    $currentTeamId = Auth::user()->team_id;
                }

                if ($currentTeamId) {
                    $query->where('team_id', $currentTeamId);
                } else {
                    // Handle missing or invalid team ID
                    $this->reports = collect();
                    session()->flash('error', 'No team selected. Please select a valid team.');
                    return;
                }
            }
        } else {
            // Unauthenticated users: No reports available
            $this->reports = collect();
            session()->flash('error', 'Access denied. Please log in.');
            return;
        }

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

        // Execute the query and set the filtered reports
        $this->reports = $query->get();
    }

    /**
     * Trigger dynamic filtering of reports.
     */
    public function filterReports()
    {
        $this->fetchReports();
    }

    /**
     * Export filtered reports to Excel.
     */
    public function exportExcel()
    {
        if ($this->reports->isEmpty()) {
            session()->flash('error', 'No reports available to export.');
            return;
        }

        return Excel::download(new ReportsExport($this->reports), 'reports-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Render the Livewire view.
     */
    public function render()
    {
        return view('livewire.summary', [
            'reports' => $this->reports,
        ]);
    }
}
