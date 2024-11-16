<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Item; // Make sure you import the correct model

class Summary extends Component
{
    public $dateRange = ''; // Date range variable
    public $search = ''; // Search term
    public $reports = []; // Store filtered reports

    // Fetch initial reports data
    public function mount()
    {
        $this->reports = Item::all(); // Fetch all records by default
    }

    // Method to filter reports based on date range and search term
    public function filterReports()
    {
        $query = Item::query();

        // Filter by search term if provided
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%'); // Filter by name
        }

        // Filter by date range if provided
        if ($this->dateRange) {
            $dates = explode(' - ', $this->dateRange); // Assuming format is 'YYYY-MM-DD - YYYY-MM-DD'
            if (count($dates) == 2) {
                $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }

        // Get filtered reports
        $this->reports = $query->get();
    }

    // Render the Livewire component view
    public function render()
    {
        return view('livewire.summary');
    }
}
