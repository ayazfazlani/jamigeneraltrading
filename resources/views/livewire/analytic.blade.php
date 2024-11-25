<div class="flex flex-1 z-0 flex-col min-h-screen max-w-screen overflow-x-auto bg text-gray-700">
    <div class="p-6 flex justify-between bg-white ">
        <h2 class="text-2xl font-semibold">Reports - Analytics</h2>
       
            <button wire:click="exportExcel" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600">Export to 
    </div>

    <div class="flex flex-grow flex-col p-4 gap-6">
        <!-- Export Button (Above Filter Section) -->
        {{-- <div class="flex justify-end mb-4">
            <button wire:click="exportExcel" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600">Export to Excel</button>
        </div> --}}

        <!-- Filter Section -->
        <div class="w-full bg-white px-4 rounded-lg ">
            <h3 class="text-lg font-semibold mb-4">Filter</h3>
            <div class="flex gap-6">
                <div class="flex items-center gap-2">
                    <label for="item_name" class="text-sm font-medium">Filter by Name</label>
                    <input type="text" wire:model="filterName" id="item_name" class="p-2 border rounded-lg" placeholder="Enter item name" />
                </div>
                {{-- <div class="flex items-center gap-2">
                    <label for="date_from" class="text-sm font-medium">From</label>
                    <input type="date" wire:model="dateFrom" id="date_from" class="p-2 border rounded-lg" />
                    <label for="date_to" class="text-sm font-medium">To</label>
                    <input type="date" wire:model="dateTo" id="date_to" class="p-2 border rounded-lg" />
                </div> --}}
                <div class="flex items-center">
                    <button wire:click="fetchData" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Apply Filters</button>
                </div>
            </div>
        </div>

        <!-- Main Table Section -->
        <div class="bg-white p-4 rounded-lg  overflow-x-auto max-h-[400px] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Item Analytics</h3>
            <div class="flex overflow-hidden">
                <!-- Left Section: Item Info -->
                <div class="w-1/4 bg-gray-50 flex-none">
                    <table class="min-w-full bg-white border rounded-lg table-auto">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="h-[48px] text-sm">Item Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach (json_decode($itemsDataJson, true) as $item) --}}
                            @foreach (json_decode($filteredAnalyticsDataJson, true) as $data)
                                <tr class="pr-3 border-b hover:bg-gray-50">
                                    <td class="h-[48px] p-3  text-sm">{{ $data['item_name'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Right Section: Analytics Table -->
                <div class="flex-1 overflow-x-auto">
                    <table class="min-w-full bg-white border rounded-lg table-auto">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="p-3 min-w-[200px]">Item</th>
                                <th class="p-3 min-w-[200px]">Current Quantity</th>
                                <th class="p-3 min-w-[200px]">Inventory Assets</th>
                                <th class="p-3 min-w-[200px]">Average Quantity</th>
                                <th class="p-3 min-w-[200px]">Turnover Ratio</th>
                                <th class="p-3 min-w-[200px]">Stock Out Days</th>
                                <th class="p-3 min-w-[200px]">Total Stock Out</th>
                                <th class="p-3 min-w-[200px]">Avg Daily Stock In</th>
                                <th class="p-3 min-w-[200px]">Avg Daily Stock Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (json_decode($filteredAnalyticsDataJson, true) as $data)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3">{{ $data['item_name'] }}</td>
                                    <td class="p-3">{{ $data['current_quantity'] }}</td>
                                    <td class="p-3">${{ number_format($data['inventory_assets'], 2) }}</td>
                                    <td class="p-3">{{ $data['average_quantity'] }}</td>
                                    <td class="p-3">{{ $data['turnover_ratio'] }}</td>
                                    <td class="p-3">{{ $data['stock_out_days_estimate'] }}</td>
                                    <td class="p-3">{{ $data['total_stock_out'] }}</td>
                                    <td class="p-3">{{ $data['avg_daily_stock_in'] }}</td>
                                    <td class="p-3">{{ $data['avg_daily_stock_out'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-200">
                                <td class="p-3 font-semibold">Total</td>
                                <td class="p-3">{{ $this->calculate('current_quantity') }}</td>
                                <td class="p-3">{{ $this->calculate('inventory_assets') }}</td>
                                <td class="p-3">{{ $this->calculate('average_quantity') }}</td>
                                <td class="p-3">{{ $this->calculate('turnover_ratio') }}</td>
                                <td class="p-3">{{ $this->calculate('stock_out_days_estimate') }}</td>
                                <td class="p-3">{{ $this->calculate('total_stock_out') }}</td>
                                <td class="p-3">{{ $this->calculate('avg_daily_stock_in') }}</td>
                                <td class="p-3">{{ $this->calculate('avg_daily_stock_out') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>