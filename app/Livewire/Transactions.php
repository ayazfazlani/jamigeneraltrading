<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Item;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

class Transactions extends Component
{
    public $transactions = [];
    public $selectedTransaction = null;
    public $filter = '';
    public $dateRange = [
        'start' => '',
        'end' => ''
    ];

    // Fetch transactions when the component is mounted
    public function mount()
    {
        $this->fetchTransactions();
    }

    // Fetch all transactions from the database
    public function fetchTransactions()
    {
        try {
            $query = Transaction::query();

            // Check user role and filter by team_id if necessary
            if (auth()->user()->hasRole('super admin')) {
                // Super admin sees all transactions
                $this->transactions = $query->get();
            } else {
                // Other users see only transactions related to their team
                $teamId = auth()->user()->team_id;
                $query->where('team_id', $teamId);
            }

            // Apply filters
            if ($this->filter) {
                $query->where(function ($query) {
                    $query->where('item_name', 'like', '%' . $this->filter . '%')
                        ->orWhere('type', 'like', '%' . $this->filter . '%');
                });
            }

            // Apply date range filter
            if (!empty($this->dateRange['start']) && !empty($this->dateRange['end'])) {
                $query->whereBetween('date', [$this->dateRange['start'], $this->dateRange['end']]);
            }

            $this->transactions = $query->get();
        } catch (\Exception $e) {
            session()->flash('error', 'Error fetching transactions: ' . $e->getMessage());
        }
    }

    // Handle selecting a transaction
    public function handleTransactionClick($transactionId)
    {
        $this->selectedTransaction = $this->transactions->firstWhere('id', $transactionId);
    }

    // Export to Excel
    public function exportToExcel()
    {
        return Excel::download(new TransactionsExport($this->transactions), 'transactions.xlsx');
    }

    // Handle filter button click
    public function applyFilters()
    {
        $this->fetchTransactions();
    }

    // Get color for transaction type
    public function getTransactionColor($type)
    {
        return match ($type) {
            'stock in' => 'bg-green-100',
            'stock out' => 'bg-red-100',
            default => 'bg-gray-100',
        };
    }

    public function render()
    {
        return view('livewire.transactions', [
            'filteredTransactions' => $this->transactions
        ]);
    }
}
