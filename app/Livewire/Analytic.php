<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Analytics;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class Analytic extends Component
{
    public $itemsDataJsn;
    public $filteredAnalyticsDataJsn;
    public $filterName = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $team_id;

    public function mount()
    {
        // Initialize team_id during mount
        $teamId = $this->team_id = (int) session('current_team_id');

        if (!$teamId) {
            $this->team_id = auth()->user()->team_id;
        }
        $this->fetchData();
    }

    public function updatedFilterName()
    {
        $this->fetchData();
    }

    public function updatedDateFrom()
    {
        $this->fetchData();
    }

    public function updatedDateTo()
    {
        $this->fetchData();
    }

    // public function fetchData()
    // {
    //     // Fetch items based on user role
    //     $items = auth()->user()->hasRole('super admin')
    //         ? Item::all()
    //         : Item::where('team_id', $this->team_id)->get();

    //     // Convert items to JSON and assign
    //     $this->itemsDataJsn = $items->toJson();

    //     // Build analytics query
    //     $query = Analytics::query();

    //     if (!empty($this->filterName)) {
    //         $query->where('item_name', 'like', '%' . $this->filterName . '%');
    //     }

    //     if (!empty($this->dateFrom)) {
    //         $query->where('date', '>=', $this->dateFrom);
    //     }

    //     if (!empty($this->dateTo)) {
    //         $query->where('date', '<=', $this->dateTo);
    //     }

    //     // Fetch analytics data and convert to JSON
    //     $this->filteredAnalyticsDataJsn = $query->get()->toJson();
    // }

    public function fetchData()
    {
        // Fetch items based on user role
        $items = auth()->user()->hasRole('super admin')
            ? Item::all()
            : Item::where('team_id', $this->team_id)->get();

        // Convert items to JSON and assign
        $this->itemsDataJsn = $items->toJson();

        // Build analytics query based on user role
        $query = Analytics::query();

        if (!auth()->user()->hasRole('super admin')) {
            $query->where('team_id', $this->team_id);
        }

        // Apply additional filters for analytics
        if (!empty($this->filterName)) {
            $query->where('item_name', 'like', '%' . $this->filterName . '%');
        }

        if (!empty($this->dateFrom)) {
            $query->where('date', '>=', $this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $query->where('date', '<=', $this->dateTo);
        }

        // Fetch analytics data and convert to JSON
        $this->filteredAnalyticsDataJsn = $query->get()->toJson();
    }

    public function exportExcel()
    {
        // Convert JSON data to a collection for export
        $analyticsData = collect(json_decode($this->filteredAnalyticsDataJsn, true));
        return Excel::download(new AnalyticsExport($analyticsData), 'analytics.xlsx');
    }

    public function calculate($column)
    {
        // Decode analytics data and calculate total
        $data = json_decode($this->filteredAnalyticsDataJsn, true);
        $total = array_sum(array_column($data, $column));
        return $total;
    }

    public function render()
    {
        return view('livewire.analytic', [
            'itemsDataJson' => $this->itemsDataJsn,
            'filteredAnalyticsDataJson' => $this->filteredAnalyticsDataJsn,

            // dd($this->filteredAnalyticsDataJsn)
        ]);
    }
}
