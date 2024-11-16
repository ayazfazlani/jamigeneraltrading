<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;

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
            $this->transactions = Transaction::all(); // Get all transactions
        } catch (\Exception $e) {
            session()->flash('error', 'Error fetching transactions.');
        }
    }

    // Handle selecting a transaction
    public function handleTransactionClick($transactionId)
    {
        $this->selectedTransaction = $this->transactions->firstWhere('id', $transactionId);
    }

    // Update filter and refresh the transaction list
    public function updatedFilter()
    {
        // This will trigger a re-render when the filter value is updated
    }

    // Get color for transaction type (StockIn, StockOut, etc.)
    public function getTransactionColor($type)
    {
        return match ($type) {
            'StockIn' => 'bg-green-100',
            'StockOut' => 'bg-red-100',
            default => 'bg-gray-100'
        };
    }

    public function render()
    {
        // Ensure $this->transactions is a collection (should be after fetchTransactions)
        $transactionsCollection = collect($this->transactions);

        // Filter transactions based on search filter
        $filteredTransactions = $transactionsCollection->filter(function ($transaction) {
            return str_contains(strtolower($transaction->type), strtolower($this->filter)) ||
                str_contains(strtolower($transaction->details), strtolower($this->filter));
        });

        // Apply date range filter if specified
        if ($this->dateRange['start'] && $this->dateRange['end']) {
            $filteredTransactions = $filteredTransactions->filter(function ($transaction) {
                $transactionDate = strtotime($transaction->date);
                $startDate = strtotime($this->dateRange['start']);
                $endDate = strtotime($this->dateRange['end']);

                return $transactionDate >= $startDate && $transactionDate <= $endDate;
            });
        }

        return view('livewire.transactions', [
            'filteredTransactions' => $filteredTransactions
        ]);
    }
}
