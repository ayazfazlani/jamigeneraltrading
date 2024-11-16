<div class="flex flex-1 z-0 flex-col min-h-screen max-w-screen overflow-x-auto bg-gray-100 text-gray-700">
    <div class="p-6 bg-white shadow-md">
        <h2 class="text-2xl font-semibold">Reports - Analytics</h2>
    </div>

    <div class="flex flex-grow p-6 gap-6">
        <div class="w-1/6 bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Items</h3>
            <ul class="space-y-3">
                @foreach ($itemsData as $item)
                    <li class="p-3 border rounded-lg shadow-sm">
                        {{ $item['name'] }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="flex-1 bg-white p-4 rounded-lg shadow-md overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Item Analytics</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border rounded-lg">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 min-w-[180px]">Item</th>
                            <th class="p-3 min-w-[180px]">Current Quantity</th>
                            <th class="p-3 min-w-[180px]">Inventory Assets</th>
                            <th class="p-3 min-w-[180px]">Average Quantity</th>
                            <th class="p-3 min-w-[180px]">Turnover Ratio</th>
                            <th class="p-3 min-w-[180px]">Stock Out (Days)</th>
                            <th class="p-3 min-w-[180px]">Total Stock Out</th>
                            <th class="p-3 min-w-[180px]">Avg Daily Stock In</th>
                            <th class="p-3 min-w-[180px]">Avg Daily Stock Out</th>
                        </tr>
                        <tr>
                            @foreach (['current_quantity', 'inventory_assets', 'average_quantity', 'turnover_ratio', 'stock_out_days_estimate', 'total_stock_out', 'avg_daily_stock_in', 'avg_daily_stock_out'] as $column)
                                <th class="p-3">
                                    <select wire:model="selectedCalculation.{{ $column }}" class="px-2 py-1 rounded border bg-gray-50 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="none">Select Calculation</option>
                                        <option value="average">Average</option>
                                        <option value="max">Max</option>
                                        <option value="min">Min</option>
                                        <option value="count">Count</option>
                                        <option value="total">Total</option>
                                    </select>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analyticsData as $data)
                            <tr class="border-b hover:bg-gray-50">
                                {{-- <td class="p-3">{{ $data->item->name }}</td> --}}
                                <td class="p-3">{{ $data->current_quantity }}</td>
                                <td class="p-3">${{ $data->inventory_assets }}</td>
                                <td class="p-3">{{ $data->average_quantity }}</td>
                                <td class="p-3">{{ $data->turnover_ratio }}</td>
                                <td class="p-3">{{ $data->stock_out_days_estimate }}</td>
                                <td class="p-3">{{ $data->total_stock_out }}</td>
                                <td class="p-3">{{ $data->avg_daily_stock_in }}</td>
                                <td class="p-3">{{ $data->avg_daily_stock_out }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-200">
                            <td class="p-3 font-semibold">Total</td>
                            <td class="p-3">{{ $this->calculate('current_quantity', $selectedCalculation['current_quantity'] ?? '') }}</td>
                            <td class="p-3">{{ $this->calculate('inventory_assets', $selectedCalculation['inventory_assets'] ?? '') }}</td>
                            <td class="p-3">{{ $this->calculate('average_quantity', $selectedCalculation['average_quantity'] ?? '') }}</td>
                            <td class="p-3">{{ $this->calculate('turnover_ratio', $selectedCalculation['turnover_ratio'] ?? '') }}</td>
                            <td class="p-3">{{ $this->calculate('stock_out_days_estimate', $selectedCalculation['stock_out_days_estimate'] ?? '') }}</td>
                            <td class="p-3">{{ $this->calculate('total_stock_out', $selectedCalculation['total_stock_out'] ?? '') }}</td>
                            <td class="p-3">{{ $this->calculate('avg_daily_stock_in', $selectedCalculation['avg_daily_stock_in'] ?? '') }}</td>
                            <td class="p-3">{{ $this->calculate('avg_daily_stock_out', $selectedCalculation['avg_daily_stock_out'] ?? '') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
