<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Item([
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'name' => $row[0],
            'quantity' => $row[1],
            'team_id' => auth()->user()->team_id,
            'cost' => 0,
            'price' => 0,

        ]);
    }
}
