<?php

namespace App\Livewire;

use App\Models\Item;
use App\Models\Analytics;
use Livewire\Component;

class Analytic extends Component
{
    public $itemsData = [];
    public $analyticsData = [];
    public $selectedCalculation = [];

    public function mount()
    {
        // Fetch the items and analytics data asynchronously
        $this->itemsData = Item::all();
        $this->analyticsData = Analytics::all();
    }

    // Handle dynamic calculation based on selected formula
    public function calculate($field, $calculation)
    {
        // Get the values of the specified field, as a collection
        $values = $this->analyticsData->pluck($field)->map(function ($item) {
            return is_numeric($item) ? (float) $item : 0;
        });

        // Perform the calculation based on the selected operation
        switch ($calculation) {
            case 'average':
                return $values->isNotEmpty() ? number_format($values->sum() / $values->count(), 2) : 0;
            case 'max':
                return number_format($values->max(), 2);
            case 'min':
                return number_format($values->min(), 2);
            case 'count':
                return $values->count();
            case 'total':
                return number_format($values->sum(), 2);
            default:
                return '';
        }
    }

    // Update the selected calculation for a specific column
    public function handleCalculationChange($column, $calculation)
    {
        // Update the selected calculation for the specified column
        $this->selectedCalculation[$column] = $calculation;
    }

    public function render()
    {
        return view('livewire.analytic', [
            'itemsData' => $this->itemsData,
            'analyticsData' => $this->analyticsData,
            'selectedCalculation' => $this->selectedCalculation,
        ]);
    }
}
