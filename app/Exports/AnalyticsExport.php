<?php

namespace App\Exports;

use App\Models\Analytics;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AnalyticsExport implements FromCollection, WithHeadings
{
    protected $filteredAnalyticsDataJson;

    public function __construct($filteredAnalyticsDataJson)
    {
        $this->filteredAnalyticsDataJson = json_decode($filteredAnalyticsDataJson, true);
    }

    public function collection()
    {
        return collect($this->filteredAnalyticsDataJson);
    }

    public function headings(): array
    {
        return ['Item id', 'item id', 'item name', 'Current Quantity', 'Inventory Assets', 'Average Quantity', 'Turnover Ratio', 'Stock Out Days', 'Total Stock Out', 'Avg Daily Stock In', 'Avg Daily Stock out'];
    }
}
