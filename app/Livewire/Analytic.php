<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Analytics;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class Analytic extends Component
{
    public $itemsDataJson;
    public $filteredAnalyticsDataJson;
    public $filterName = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
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

    public function fetchData()
    {
        // Fetch items
        $items = Item::all();
        $this->itemsDataJson = $items->toJson();

        // Fetch analytics data with filters
        $query = Analytics::query();

        if ($this->filterName) {
            // Search for 'item_name' directly as a column
            $query->where('item_name', 'like', '%' . $this->filterName . '%');
        }

        if ($this->dateFrom) {
            $query->where('date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('date', '<=', $this->dateTo);
        }

        $this->filteredAnalyticsDataJson = $query->get()->toJson();
    }


    public function exportExcel()
    {
        return Excel::download(new AnalyticsExport($this->filteredAnalyticsDataJson), 'analytics.xlsx');
    }

    public function calculate($column)
    {
        $data = json_decode($this->filteredAnalyticsDataJson, true);
        $total = 0;

        foreach ($data as $row) {
            if (isset($row[$column])) {
                $total += $row[$column];
            }
        }

        return $total;
    }

    public function render()
    {
        return view('livewire.analytic', [
            'itemsDataJson' => $this->itemsDataJson,
            'filteredAnalyticsDataJson' => $this->filteredAnalyticsDataJson
        ]);
    }
}
