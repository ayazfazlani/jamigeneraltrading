<div class="p-6 max-h-screen overflow-auto bg-white text-gray-900">
    <!-- Header Section -->
    @if(session()->has('message'))
    <div class="p-4 right-0 mb-4 text-sm text-white bg-green-400" role="alert">
        {{ session('message') }}
    </div>
    @elseif(session()->has('error'))
        <div class="p-4 mb-4 text-sm text-white bg-red-400" role="alert">
            {{ session('error') }}
        </div>
    @endif
   
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Stock In</h1>
        <button wire:click="loadItems" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded">Reset</button>
    </div>

    <hr class="my-4 border-gray-300" />

    <div class="flex gap-8 mb-6">
        <!-- Left Section: Select Items -->
        <div class="flex-1 border p-4 rounded-lg shadow-sm bg-white">
            <h2 class="text-lg font-semibold text-gray-600 mb-4">Select Items</h2>
            @role('viewer')
            @else
            <button wire:click="$set('isModalOpen', true)" class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded">+ Add Item</button>
            @endrole
            <hr class="mb-4" />
            <ul class="space-y-2 max-h-96 overflow-auto">
                @foreach($items as $item)
                    <li class="flex justify-between items-center p-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer" wire:click="toggleItemSelection({{ $item['id'] }})">
                        <span>{{ $item['name'] }}</span>
                        <span>Quantity: {{ $item['quantity'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Right Section: Stock In -->
        <div class="flex-1 border p-4 rounded-lg shadow-sm bg-white">
            <h2 class="text-lg font-semibold text-gray-600 mb-4">Stock In</h2>
            <hr class="mb-4" />
            <div>
                @if(empty($selectedItems))
                    <p class="text-gray-500 italic">No items selected for stock in</p>
                @else
                    <ul class="space-y-2 mb-4">
                        @foreach($selectedItems as $item)
                            <li class="flex justify-between items-center p-2 border border-gray-300 rounded-md">
                                <span>{{ $item['name'] }}</span>
                                <input type="number" min="1" wire:model.defer="selectedItems.{{ $loop->index }}.quantity" class="w-20 p-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="flex justify-around items-center mt-4 mb-4 border-t pt-4">
        <div class="text-gray-600">
            <p>Total number of items: {{ count($selectedItems) }}</p>
            <p>Total quantity: {{ array_sum(array_column($selectedItems, 'quantity')) }}</p>
        </div>
        @role('viewer')
        @else
        <button wire:click="handleStockIn" class="py-2 px-4 rounded bg-green-500 hover:bg-green-600 text-white">Stock In</button>
        @endrole
    </div>

    <!-- Modal to Add New Item -->
    @if($isModalOpen)
    <div class="fixed inset-0 bg-gray-700 bg-opacity-50 overflow-y-auto flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-4 text-gray-600">Add New Item</h3>
            <div class="space-y-4">
                <input type="text" wire:model="newItem.sku" placeholder="SKU" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <input type="text" wire:model="newItem.name" placeholder="Name" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <input type="number" wire:model="newItem.cost" placeholder="Cost" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <input type="number" wire:model="newItem.price" placeholder="Price" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <input type="text" wire:model="newItem.type" placeholder="Type" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <input type="text" wire:model="newItem.brand" placeholder="Brand" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <input type="number" wire:model="newItem.quantity" placeholder="Quantity" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <input type="file" wire:model="newItem.image" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300">
                <div class="flex justify-end mt-4">
                    <button wire:click="addItem" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
                    <button wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md ml-2 hover:bg-gray-400">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endif

</div>
