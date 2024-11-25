<div>
    <div class="p-6 flex-1 z-0 max-h-screen overflow-auto bg-white text-gray-900">
        <!-- Page Header -->
        @if(session()->has('message'))
    <div class="p-4 mb-4 text-sm text-white bg-green-400 rounded-lg" role="alert">
            {{ session('message') }}
        </div>
    @elseif(session()->has('error'))
        <div class="p-4 mb-4 text-sm text-white bg-red-400 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold">Stock Out</h1>
            <button wire:click="resetSelection" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded">Reset</button>
        </div>
    
        <!-- Divider -->
        <hr class="my-4 border-gray-300" />
    
        <!-- Select Items and Stock Out Section -->
        <div class="flex gap-8 mb-6">
            <div class="flex-1 border p-4 rounded-lg shadow-sm bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-600">Select Items</h2>
                </div>
                <hr class="mb-4" />
    
                <ul class="space-y-2 max-h-96 overflow-auto">
                    @foreach($items as $item)
                        <li wire:click="toggleItemSelection({{ $item['id'] }})"
                            class="flex justify-between items-center p-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer {{ in_array($item['id'], array_column($selectedItems, 'id')) ? 'bg-gray-100' : '' }}">
                            <span>{{ $item['name'] }}</span>
                            <span>Quantity: {{ $item['quantity'] ?? 0 }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
    
            <div class="flex-1 border p-4 rounded-lg shadow-sm bg-white">
                <h2 class="text-lg font-semibold text-gray-600 mb-4">Stock Out</h2>
                <hr class="mb-4" />
    
                @if(count($selectedItems) === 0)
                    <p class="text-gray-500 italic">No items selected for stock out</p>
                @else
                    <ul class="space-y-2 mb-4">
                        @foreach($selectedItems as $item)
                            <li class="flex justify-between items-center p-2 border border-gray-300 rounded-md">
                                <span>{{ $item['name'] }}</span>
                                <input type="number" min="1" wire:model="selectedItems.{{ $loop->index }}.quantity"
                                       class="w-20 p-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    
        <!-- Totals and Buttons -->
        <div class="flex justify-around items-center mt-4 mb-4 border-t pt-4">
            <div class="text-gray-600">
                <p>Total number of items: {{ count($selectedItems) }}</p>
                <p>Total quantity: {{ array_sum(array_column($selectedItems, 'quantity')) }}</p>
            </div>
      @role('viewer')
      @else
            <button wire:click="handleStockOut" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                Stock Out
            </button>
      @endrole
        </div>
    </div>
    
</div>
