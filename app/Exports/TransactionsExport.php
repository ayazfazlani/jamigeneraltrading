<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Type',
            'Item Name',
            'Quantity',
            'Unit Price',
            'Total Price',
            'Date',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->type,
            $transaction->item_name,
            $transaction->quantity,
            $transaction->unit_price,
            $transaction->total_price,
            $transaction->date,
        ];
    }
}
