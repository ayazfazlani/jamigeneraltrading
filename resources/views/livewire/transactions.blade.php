<div>
    <div class="p-6 z-0 flex-1 bg-white min-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Transactions</h1>
            <button wire:click="exportToExcel" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                Export to Excel
            </button>
        </div>
    
        <div class="flex items-center gap-4 mb-4">
            <div class="relative">
                <input
                    type="text"
                    wire:model="filter"
                    class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Search by tags..."
                />
            </div>
    
            <div class="relative">
                <input
                    type="date"
                    wire:model="dateRange.start"
                    class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300"
                />
                <span class="mx-2">to</span>
                <input
                    type="date"
                    wire:model="dateRange.end"
                    class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300"
                />
            </div>
        </div>
    
        <div class="flex">
            <div class="w-2/4 pr-4 h-full max-h-[400px] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-2 text-gray-900">Transaction List</h2>
                <ul class="space-y-2 border border-gray-200 rounded-md p-4 bg-white">
                    @foreach($filteredTransactions as $transaction)
                        <li
                            wire:click="handleTransactionClick({{ $transaction['id'] }})"
                            class="p-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer {{ $this->getTransactionColor($transaction['type']) }}"
                        >
                            <div class="flex justify-between items-center">
                                <span class="text-gray-900">{{ $transaction['type'] }}</span>
                                <span class="text-gray-500">{{ $transaction['details'] }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
    
            <div class="w-2/4 pl-4 h-full max-h-[400px] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-2 text-gray-900">Transaction Details</h2>
                @if($selectedTransaction)
                    <div class="p-4 border border-gray-200 rounded-md bg-white">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $selectedTransaction['type'] }}</h3>
                        <p class="text-gray-700"><strong>Item Title:</strong> {{ $selectedTransaction['item']['name'] ?? 'N/A' }}</p>
                        <p class="text-gray-700"><strong>Details:</strong> {{ $selectedTransaction['details'] ?? 'N/A' }}</p>
                        <p class="text-gray-700"><strong>Date:</strong> {{ $selectedTransaction['date'] ?? 'N/A' }}</p>
                        @if(isset($selectedTransaction['quantity']))
                            <p class="text-gray-700"><strong>Quantity:</strong> {{ $selectedTransaction['quantity'] }}</p>
                        @endif
                        @if(isset($selectedTransaction['price']))
                            <p class="text-gray-700"><strong>Price:</strong> ${{ $selectedTransaction['price'] }}</p>
                        @endif
                        @if(isset($selectedTransaction['stock']))
                            <p class="text-gray-700"><strong>Stock:</strong> {{ $selectedTransaction['stock'] }}</p>
                        @endif
                    </div>
                @else
                    <div class="p-4 border border-gray-200 rounded-md bg-white">
                        <p class="text-gray-700">Please select a transaction to see details.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
</div>
