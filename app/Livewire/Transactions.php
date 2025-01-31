<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Illuminate\Support\Facades\Auth;

class Transactions extends Component
{
    public $transactions;
    public $selectedTransaction = null;
    public $filter = '';
    public $dateRange = [
        'start' => '',
        'end' => ''
    ];
    // app/Models/Transaction.php
    protected $casts = [
        'date' => 'datetime',
    ];
    public function mount()
    {
        $this->fetchTransactions();
    }

    public function fetchTransactions()
    {
        try {
            $query = Transaction::query();

            // Apply team filter for non-super admins
            if (!Auth::user()->hasRole('super admin')) {
                $teamId = Auth::user()->getCurrentTeamId();
                $query->when($teamId, fn($q) => $q->where('team_id', $teamId));
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
                $query->whereBetween('date', [
                    $this->dateRange['start'],
                    $this->dateRange['end']
                ]);
            }

            $this->transactions = $query->get();
            $this->selectedTransaction = null; // Reset selection on new data

        } catch (\Exception $e) {
            session()->flash('error', 'Error fetching transactions: ' . $e->getMessage());
        }
    }

    public function updatedFilter()
    {
        $this->fetchTransactions();
    }

    public function updatedDateRange()
    {
        if ($this->dateRange['start'] && $this->dateRange['end']) {
            $this->fetchTransactions();
        }
    }

    public function switchTeam($teamId)
    {
        try {
            if (!Auth::user()->teams()->where('team_id', $teamId)->exists()) {
                abort(403, 'Unauthorized team selection');
            }

            session(['current_team_id' => $teamId]);
            Auth::user()->update(['current_team_id' => $teamId]);
            $this->fetchTransactions();
        } catch (\Exception $e) {
            session()->flash('error', 'Error switching team: ' . $e->getMessage());
        }
    }

    public function handleTransactionClick($transactionId)
    {
        $this->selectedTransaction = Transaction::find($transactionId);
    }

    public function getTransactionColor($type)
    {
        return match ($type) {
            'stock in' => 'bg-green-100',
            'stock out' => 'bg-red-100',
            default => 'bg-gray-100',
        };
    }

    public function exportToExcel()
    {
        if ($this->transactions->isEmpty()) {
            session()->flash('error', 'No transactions available to export.');
            return;
        }

        return Excel::download(
            new TransactionsExport($this->transactions),
            'transactions-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.transactions');
    }
}
