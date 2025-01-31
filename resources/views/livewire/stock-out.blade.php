<div class="p-6 max-h-screen overflow-auto bg-white text-gray-900">
    <!-- Notifications -->
    @if(session()->has('message'))
    <div class="p-4 mb-4 text-sm text-white bg-green-500 rounded-lg" role="alert">
        {{ session('message') }}
    </div>
    @elseif(session()->has('error'))
    <div class="p-4 mb-4 text-sm text-white bg-red-500 rounded-lg" role="alert">
        {{ session('error') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Stock Out Management</h1>
        <div class="flex gap-2">
            <button wire:click="loadItems" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md">
                Refresh Items
            </button>
        </div>
    </div>

    <!-- Search and Date Filter -->
    <div class="flex items-center gap-4 mb-6">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Search items by name or SKU..."
                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="dateRange.start" 
                class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            <span class="text-gray-500">to</span>
            <input type="date" wire:model.live="dateRange.end" 
                class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Stock Out Operations -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold">Stock Out Operations</h2>
                </div>
                <div class="p-4 space-y-4">
                    <!-- Item Selection -->
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-medium">Select Items</h3>
                        </div>
                        <ul class="space-y-2 max-h-96 overflow-auto">
                            @foreach($items as $item)
                            <li wire:key="item-{{ $item->id }}" 
                                wire:click="toggleItemSelection({{ $item->id }})" 
                                class="p-3 border rounded-md cursor-pointer hover:bg-gray-50 {{ in_array($item->id, array_column($selectedItems, 'id')) ? 'bg-red-50 border-red-200' : '' }}">
                                <div class="flex justify-between items-center">
                                    <span>{{ $item->name }}</span>
                                    <span class="text-sm {{ $item->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Qty: {{ $item->quantity }}
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Selected Items -->
                    @if(count($selectedItems) > 0)
                    <div class="border rounded-lg p-4">
                        <h3 class="font-medium mb-3">Selected for Stock Out</h3>
                        <ul class="space-y-2">
                            @foreach($selectedItems as $index => $item)
                            <li wire:key="selected-{{ $item['id'] }}" 
                                class="flex justify-between items-center p-2 border rounded-md bg-gray-50">
                                <span>{{ $item['name'] }}</span>
                                {{-- <input type="number" 
                                    min="1" 
                                    max="{{ Item::find($item['id'])->quantity }}"
                                    wire:model.debounce.300ms="selectedItems.{{ $index }}.quantity" 
                                    class="w-24 px-2 py-1 border rounded-md focus:ring-blue-500 focus:border-blue-500"> --}}
                                    <input type="number" 
                                        min="1" 
                                        max="{{ \App\Models\Item::find($item['id'])->quantity }}"
                                        wire:model.debounce.300ms="selectedItems.{{ $index }}.quantity" 
                                        class="w-24 px-2 py-1 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                Total Items: {{ count($selectedItems) }} | 
                                Total Quantity: {{ array_sum(array_column($selectedItems, 'quantity')) }}
                            </div>
                            @role('viewer')
                            @else
                            <button wire:click="handleStockOut" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                <span wire:loading.remove>Confirm Stock Out</span>
                                <span wire:loading>
                                    Processing...
                                    <i class="fas fa-spinner fa-spin ml-2"></i>
                                </span>
                            </button>
                            @endrole
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Transaction History -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold">Stock Out History</h2>
            </div>
            <div class="p-4">
                <div class="overflow-auto max-h-[500px]">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Item</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Quantity</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Unit Price</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d Y') }}</td>
                                <td class="px-4 py-2 text-sm">{{ $transaction->item_name }}</td>
                                <td class="px-4 py-2 text-sm">{{ $transaction->quantity }}</td>
                                <td class="px-4 py-2 text-sm">${{ number_format($transaction->unit_price, 2) }}</td>
                                <td class="px-4 py-2 text-sm">${{ number_format($transaction->total_price, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    No stock-out transactions found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>