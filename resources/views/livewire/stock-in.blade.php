<div class="p-10 max-h-screen overflow-auto bg-white text-gray-900">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Stock In</h1>
        <button wire:click="loadItems" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded">Reset</button>
    </div>

    <hr class="my-4 border-gray-300" />

    <div class="flex gap-8 mb-6">
        <!-- Left Section: Select Items -->
        <div class="flex-1 border p-4 rounded-lg shadow-sm bg-white">
            <h2 class="text-lg font-semibold text-gray-600 mb-4">Select Items</h2>
            <button wire:click="$set('isModalOpen', true)" class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded">+ Add Item</button>
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
        <button wire:click="handleStockIn" class="py-2 px-4 rounded bg-green-500 hover:bg-green-600 text-white">Stock In</button>
    </div>

    <!-- Modal to Add New Item -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white w-96 p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Add New Item</h2>

                <form wire:submit.prevent="addItem">
                    <div class="space-y-4">
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                            <input type="text" id="sku" wire:model="newItem.sku" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" required>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" wire:model="newItem.name" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" required>
                        </div>

                        <div>
                            <label for="cost" class="block text-sm font-medium text-gray-700">Cost</label>
                            <input type="number" id="cost" wire:model="newItem.cost" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" required>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" id="price" wire:model="newItem.price" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" required>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <input type="text" id="type" wire:model="newItem.type" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" required>
                        </div>

                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" id="brand" wire:model="newItem.brand" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" required>
                        </div>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="quantity" wire:model="newItem.quantity" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-300" required>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                            <input type="file" id="image" wire:model="newItem.image" class="w-full p-2 border border-gray-300 rounded-md">
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="w-full py-2 bg-blue-500 text-white rounded-lg">Add Item</button>
                        </div>
                    </div>
                </form>
                <button wire:click="$set('isModalOpen', false)" class="absolute top-2 right-2 text-gray-500">X</button>
            </div>
        </div>
    @endif
</div>
