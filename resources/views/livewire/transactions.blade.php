<div>
    <div class="p-6 z-0 flex-1 bg-white min-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold">Transactions</h1>
            <button wire:click="exportToExcel" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                Export to Excel
            </button>
        </div>
    
        <div class="flex items-center gap-4 mb-4">
            <div class="relative flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="filter"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Search transactions..."
                />
            </div>
    
            <div class="relative flex items-center gap-2">
                <input
                    type="date"
                    wire:model.live="dateRange.start"
                    class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300"
                />
                <span class="text-gray-500">to</span>
                <input
                    type="date"
                    wire:model.live="dateRange.end"
                    class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300"
                />
            </div>
        </div>
    
        <div class="flex gap-4">
            <div class="w-1/2 pr-2 h-full max-h-[70vh] overflow-y-auto">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-700">Transaction List</h2>
                    </div>
                    <div class="p-4">
                        @forelse($transactions as $transaction)
                            <div
                                wire:key="transaction-{{ $transaction->id }}"
                                wire:click="handleTransactionClick({{ $transaction->id }})"
                                class="p-3 mb-2 cursor-pointer rounded-md {{ $this->getTransactionColor($transaction->type) }} 
                                    @if($selectedTransaction && $selectedTransaction->id === $transaction->id) border-2 border-blue-400 @else border border-gray-200 @endif"
                            >
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $transaction->item_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->type }}</p>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$transaction['created_at'])->format('M d Y'); }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">
                                No transactions found
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="w-1/2 pl-2 h-full max-h-[70vh] overflow-y-auto">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-700">Transaction Details</h2>
                    </div>
                    <div class="p-4">
                        @if($selectedTransaction)
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Type</label>
                                    <p class="mt-1 text-gray-900">{{ $selectedTransaction->type }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Item Name</label>
                                    <p class="mt-1 text-gray-900">{{ $selectedTransaction->item_name ?? 'N/A' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Quantity</label>
                                        <p class="mt-1 text-gray-900">{{ $selectedTransaction->quantity }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Unit Price</label>
                                        <p class="mt-1 text-gray-900">${{ number_format($selectedTransaction->unit_price, 2) }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Total Price</label>
                                    <p class="mt-1 text-gray-900">${{ number_format($selectedTransaction->total_price, 2) }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Date</label>
                                    <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$selectedTransaction['created_at'])->format('M d Y'); }}</p>
                                </div>
                            </div>
                        @else
                            <div class="p-4 text-center text-gray-500">
                                Select a transaction to view details
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>