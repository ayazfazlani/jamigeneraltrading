<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Illuminate\Support\Facades\Auth;

class Transactions extends Component
{
    public $transactions; // Holds transactions to display
    public $selectedTransaction = null; // Selected transaction for additional actions
    public $filter = ''; // Filter for search input
    public $dateRange = [
        'start' => '',
        'end' => ''
    ]; // Date range filter
    public $currentTeamId; // Tracks the currently selected team ID

    // Initialize component
    public function mount()
    {
        $this->currentTeamId = (int) session('current_team_id', 0); // Default to 0 if no team ID in session
        $this->fetchTransactions();
    }

    // Fetch transactions from the database
    public function fetchTransactions()
    {
        try {
            // Initialize query for transactions
            $query = Transaction::query();

            // Apply team filter if the user is not a super admin
            if (!Auth::user()->hasRole('super admin')) {
                if ($this->currentTeamId) {
                    $query->where('team_id', $this->currentTeamId); // Filter by current team ID
                } else {
                    $this->transactions = collect(); // No team selected, return empty collection
                    return;
                }
            }

            // Apply search filter
            if (!empty($this->filter)) {
                $query->where(function ($query) {
                    $query->where('item_name', 'like', '%' . $this->filter . '%')
                        ->orWhere('type', 'like', '%' . $this->filter . '%');
                });
            }

            // Apply date range filter
            if (!empty($this->dateRange['start']) && !empty($this->dateRange['end'])) {
                $query->whereBetween('date', [$this->dateRange['start'], $this->dateRange['end']]);
            }

            // Fetch transactions
            $this->transactions = $query->get();
        } catch (\Exception $e) {
            session()->flash('error', 'Error fetching transactions: ' . $e->getMessage());
        }
    }

    // Handle switching the current team
    public function switchTeam($teamId)
    {
        try {
            $this->currentTeamId = $teamId;
            session(['current_team_id' => $teamId]); // Store the selected team ID in session
            $this->fetchTransactions(); // Refresh transactions
        } catch (\Exception $e) {
            session()->flash('error', 'Error switching team: ' . $e->getMessage());
        }
    }

    // filter button click 
    // Handle filter button click
    public function applyFilters()
    {
        $this->fetchTransactions();
    }
    // Export transactions to Excel
    public function exportToExcel()
    {
        if ($this->transactions->isEmpty()) {
            session()->flash('error', 'No transactions available to export.');
            return;
        }

        return Excel::download(new TransactionsExport($this->transactions), 'transactions-' . now()->format('Y-m-d') . '.xlsx');
    }

    // Handle transaction selection
    public function handleTransactionClick($transactionId)
    {
        $this->selectedTransaction = $this->transactions->firstWhere('id', $transactionId);
    }

    // Get CSS class for transaction type
    public function getTransactionColor($type)
    {
        return match ($type) {
            'stock in' => 'bg-green-100',
            'stock out' => 'bg-red-100',
            default => 'bg-gray-100',
        };
    }

    // Render the Livewire view
    public function render()
    {
        return view('livewire.transactions', [
            'filteredTransactions' => $this->transactions,
        ]);
    }
}