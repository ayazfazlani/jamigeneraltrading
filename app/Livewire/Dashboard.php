<?php

namespace App\Livewire;

use App\Models\Analytics;
use Livewire\Component;

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
            // Super admin can see all data
            if (auth()->user()->hasRole('super admin')) {
                $this->summary = [
                    'totalInventory' => Analytics::sum('current_quantity'),
                    'stockIn' => Analytics::sum('total_stock_in'),
                    'stockOut' => Analytics::sum('total_stock_out'),
                ];
            }
            // Team admin can only see data for their team
            else
            //  if (auth()->user()->hasRole('team admin', 'viewer', 'editor'))
            {
                $this->summary = [
                    'totalInventory' => Analytics::where('team_id', auth()->user()->team_id)->sum('current_quantity'),
                    'stockIn' => Analytics::where('team_id', auth()->user()->team_id)->sum('total_stock_in'),
                    'stockOut' => Analytics::where('team_id', auth()->user()->team_id)->sum('total_stock_out'),
                ];
            }
            // else {
            //     // If the user is neither super admin nor team admin, you can define what should happen
            //     $this->summary = [
            //         'totalInventory' => 0,
            //         'stockIn' => 0,
            //         'stockOut' => 0,
            //     ];
            // }
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
            // Super admin can see all data
            if (auth()->user()->hasRole('super admin')) {
                $this->totalInventoryData = Analytics::select('item_id', 'current_quantity')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->item_id,
                            'quantity' => $item->current_quantity,
                        ];
                    });
            }
            // Team admin can only see data for their team
            else if (auth()->user()->hasRole('team admin')) {
                $this->totalInventoryData = Analytics::where('team_id', auth()->user()->team_id)
                    ->select('item_id', 'current_quantity')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->item_id,
                            'quantity' => $item->current_quantity,
                        ];
                    });
            } else {
                // If the user is neither super admin nor team admin, return empty data
                $this->totalInventoryData = collect();
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
