<div class="p-6 flex-1 z-0 items-center  bg-white  shadow">
    <div class="flex flex-1 justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-semibold">Summary</h1>
            {{-- <h1 class="text-xl text-gray-500">Reports</h1>
            <h2 class="text-2xl font-bold text-gray-500">Summary</h2> --}}
        </div>
        <button class="bg-green-500 text-white px-4 py-2 max-sm:px-1 max-sm:py-1 rounded hover:bg-green-600" wire:click="exportExcel">
            Export excel
        </button>
    </div>

    <div class="flex flex-wrap w-full items-center gap-2 mb-4">
        <!-- Search by Name input -->
        <input
            type="text"
            placeholder="Search by Name"
            class="w-full md:flex-1 border border-gray-300 rounded p-2"
            wire:model.live="search"
        />
       <div class="flex-1">
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 max-sm:px-2 max-sm:py-1"
        wire:click="filterReports">
        Apply
    </button>
       </div>
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
