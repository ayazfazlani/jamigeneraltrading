<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Analytics;
use Illuminate\Support\Facades\Auth;

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
        // Check if the user is authenticated
        if (auth()->check()) {
            if (auth()->user()->hasRole('super admin')) {
                // Super admin can see all data
                $this->summary = [
                    'totalInventory' => Analytics::sum('current_quantity'),
                    'stockIn' => Analytics::sum('total_stock_in'),
                    'stockOut' => Analytics::sum('total_stock_out'),
                ];
            } else {
                // Team admin sees data for all teams they belong to
                $teamId = (int) session('current_team_id');
                if (!$teamId) {
                    $teamId = Auth::user()->team_id;
                }
                $this->summary = [
                    'totalInventory' => Analytics::where('team_id', $teamId)->sum('current_quantity'),
                    'stockIn' => Analytics::where('team_id', $teamId)->sum('total_stock_in'),
                    'stockOut' => Analytics::where('team_id', $teamId)->sum('total_stock_out'),
                ];
            }
        } else {
            // Handle unauthenticated users
            $this->summary = [
                'totalInventory' => 0,
                'stockIn' => 0,
                'stockOut' => 0,
            ];
        }
    }

    public function fetchTotalInventoryData()
    {
        // Ensure the user is authenticated
        if (auth()->check()) {
            if (auth()->user()->hasRole('super admin')) {
                // Super admin can see all data
                $this->totalInventoryData = Analytics::select('item_id', 'current_quantity')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->item_id,
                            'quantity' => $item->current_quantity,
                        ];
                    });
            } else {
                // Team admin sees data for all teams they belong to
                $teamId = (int) session('current_team_id');
                if (!$teamId) {
                    $teamId = Auth::user()->team_id;
                }
                $this->totalInventoryData = Analytics::where('team_id', $teamId)
                    ->select('item_id', 'current_quantity')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->item_id,
                            'quantity' => $item->current_quantity,
                        ];
                    });
            }
        } else {
            // Handle unauthenticated users
            $this->totalInventoryData = collect();
        }
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'summary' => $this->summary,
            'totalInventoryData' => $this->totalInventoryData,
        ]);
    }
}
