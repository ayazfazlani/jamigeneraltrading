<?php
// app/Http/Livewire/Transactions.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
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

            // Apply filters
            if ($this->filter) {
                $query->where('item_name', 'like', '%' . $this->filter . '%')
                    ->orWhere('type', 'like', '%' . $this->filter . '%');
            }

            // Apply date range filter
            if ($this->dateRange['start'] && $this->dateRange['end']) {
                $query->whereBetween('date', [$this->dateRange['start'], $this->dateRange['end']]);
            }

            // Convert transactions to a collection
            $this->transactions = collect($query->get());
        } catch (\Exception $e) {
            session()->flash('error', 'Error fetching transactions.');
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
