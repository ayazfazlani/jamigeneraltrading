<div class="p-6 max-h-screen w-full overflow-y-auto bg-white text-gray-900">
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
        <h1 class="text-2xl font-semibold">Stock in</h1>
        <div class="flex gap-2">
            <button wire:click="loadItems" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md">
                Refresh
            </button>
        </div>
    </div>

    <!-- Search and Date Filter -->
    <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="w-full md:flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Search items by name or SKU..."
                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-full flex justify-between md:justify-end md:flex-1 items-center gap-2">
           <div>
            <input type="date" wire:model.live="dateRange.start" 
            class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 max-sm:w-36">
           </div>
            <span class="text-gray-500"> to </span>
           <div>
            <input type="date" wire:model.live="dateRange.end" 
            class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 max-sm:w-36">
           </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-sm:flex-wrap">
        <!-- Left Column: Stock Operations -->
        <div class="space-y-6">
            <!-- Stock In Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold">Stock In Operations</h2>
                </div>
                <div class="p-4 space-y-4">
                    <!-- Item Selection -->
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-medium">Select Items</h3>
                            @role('viewer')
                            @else
                            <button wire:click="$set('isModalOpen', true)" 
                                class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600">
                                + New Item
                            </button>
                            @endrole
                        </div>
                        <ul class="space-y-2 max-h-96 overflow-auto">
                            @foreach($items as $item)
                            <li wire:click="toggleItemSelection({{ $item->id }})" 
                                class="p-3 border rounded-md cursor-pointer hover:bg-gray-50 {{ in_array($item->id, array_column($selectedItems, 'id')) ? 'bg-blue-50 border-blue-200' : '' }}">
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
                        <h3 class="font-medium mb-3">Selected for Stock In</h3>
                        <ul class="space-y-2">
                            @foreach($selectedItems as $index => $item)
                            <li class="flex justify-between items-center p-2 border rounded-md bg-gray-50">
                                <span>{{ $item['name'] }}</span>
                                <input type="number" min="1" 
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
                            <button wire:click="handleStockIn" 
                                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                Confirm Stock In
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
                <h2 class="text-lg font-semibold">Stock In History</h2>
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
                                <td class="px-4 py-2 text-sm">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$transaction['created_at'])->format('M d Y'); }}</td>
                                <td class="px-4 py-2 text-sm">{{ $transaction->item_name }}</td>
                                <td class="px-4 py-2 text-sm">{{ $transaction->quantity }}</td>
                                <td class="px-4 py-2 text-sm">${{ number_format($transaction->unit_price, 2) }}</td>
                                <td class="px-4 py-2 text-sm">${{ number_format($transaction->total_price, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    No stock-in transactions found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- New Item Modal -->
    @if($isModalOpen)
    {{-- <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold">Add New Item</h3>
            </div>
            <div class="p-6 space-y-4">
                <input type="text" wire:model="newItem.sku" placeholder="SKU" 
                    class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                <input type="text" wire:model="newItem.name" placeholder="Name" 
                    class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" wire:model="newItem.cost" placeholder="Cost" 
                        class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <input type="number" wire:model="newItem.price" placeholder="Price" 
                        class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" wire:model="newItem.type" placeholder="Type" 
                        class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <input type="text" wire:model="newItem.brand" placeholder="Brand" 
                        class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <input type="number" wire:model="newItem.quantity" placeholder="Quantity" 
                    class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                <input type="file" wire:model="newItem.image" 
                    class="w-full p-2 border rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-2">
                <button wire:click="$set('isModalOpen', false)" 
                    class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-md">Cancel</button>
                <button wire:click="addItem" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Item</button>
            </div>
        </div>
    </div> --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-md rounded-lg shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold">Add New Item</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <input type="text" wire:model="newItem.sku" placeholder="SKU" 
                        class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('newItem.sku') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
    
                <div>
                    <input type="text" wire:model="newItem.name" placeholder="Name" 
                        class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('newItem.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
    
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <input type="number" wire:model="newItem.cost" placeholder="Cost" 
                            class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('newItem.cost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="number" wire:model="newItem.price" placeholder="Price" 
                            class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('newItem.price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
    
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <input type="text" wire:model="newItem.type" placeholder="Type" 
                            class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('newItem.type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="newItem.brand" placeholder="Brand" 
                            class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('newItem.brand') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
    
                <div>
                    <input type="number" wire:model="newItem.quantity" placeholder="Quantity" 
                        class="w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('newItem.quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
    
                <div>
                    <input type="file" wire:model="newItem.image" 
                        class="w-full p-2 border rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('newItem.image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end gap-2">
                <button wire:click="$set('isModalOpen', false)" 
                    class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-md">Cancel</button>
                <button wire:click="addItem" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Item</button>
            </div>
        </div>
    </div>
    
    @endif
</div>