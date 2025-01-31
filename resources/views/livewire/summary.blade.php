<div class="p-6 flex-1 z-0 items-center  bg-white  shadow">
    <div class="flex flex-1 justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-semibold">Reports - Summary</h1>
            {{-- <h1 class="text-xl text-gray-500">Reports</h1>
            <h2 class="text-2xl font-bold text-gray-500">Summary</h2> --}}
        </div>
        <button class="bg-green-500 text-black px-4 py-2 rounded hover:bg-green-600" wire:click="exportExcel">
            Export Excel
        </button>
    </div>

    <div class="flex items-center mb-4">
        <!-- Date Range input -->
        {{-- <label class="mr-2">Select Date Range:</label>
        <input
            type="text"
            placeholder="Select Date Range"
            class="border border-gray-300 rounded p-2 mr-2"
            wire:model="dateRange"
        /> --}}
        <!-- Search by Name input -->
        <input
            type="text"
            placeholder="Search by Name"
            class="border border-gray-300 rounded p-2 mr-2"
            wire:model="search"
        />
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" wire:click="filterReports">
            Apply Filters
        </button>
    </div>

    <div class="overflow-x-auto overflow-x-auto max-h-[400px] overflow-y-auto">
        <table class="min-w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border text-gray-500 border-gray-300 px-4 py-2">Name</th>
                    <th class="border text-gray-500 border-gray-300 px-4 py-2">Stock In</th>
                    <th class="border text-gray-500 border-gray-300 px-4 py-2">Stock Out</th>
                    <th class="border text-gray-500 border-gray-300 px-4 py-2">Adjustments</th>
                    <th class="border text-gray-500 border-gray-300 px-4 py-2">Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $report->item_name }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $report->total_stock_in }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $report->total_stock_out }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $report->current_quantity }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $report->inventory_assets}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colSpan="5" class="text-center py-4 text-gray-500">No results found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
