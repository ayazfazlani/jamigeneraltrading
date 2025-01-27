<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
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
        $teamId = session('current_team_id');


        // dump($teamId);
        if (!$teamId) {
            // Handle the case where no team is active
            // session()->flash('error', 'No active team selected.');
            $teamId = Auth::user()->team_id;
        }
        return new Item([
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'name' => $row[0],
            'quantity' => $row[1],
            'team_id' =>  $teamId,
            'cost' => $row[2],
            'price' => 0,

        ]);
    }
}
