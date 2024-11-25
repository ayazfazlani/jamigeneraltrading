<div>
    <div class="p-8 flex-1 bg-white">
        <!-- Heading and Add Button -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Item List</h1>
           @role('viewer')
           @else
            <button
                wire:click="toggleModal"
                class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Item</span>
            </button>
            @endrole
        </div>
    
        <!-- Search Bar and In-Stock Button -->
        <div class="flex items-center space-x-4 mb-4">
            <input
                type="text"
                placeholder="Search for an item..."
                class="w-half pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
            />
            <button
                wire:click="$toggle('inStockOnly')"
                class="px-4 py-2 border rounded-md {{ $inStockOnly ? 'bg-green-500 text-white' : 'bg-white text-gray-700 border-gray-300' }}"
            >
                <span>In Stock</span>
            </button>
        </div>
    
        <!-- Left and Right Sections for Product List and Product Details -->
        <div class="flex space-x-6">
            <!-- Left Section: Product List -->
            <div class="w-2/4">
                <div class="space-y-4 max-h-[400px] overflow-y-auto">
                    @foreach($items as $item)
                        <div
                            class="p-4 bg-white shadow rounded-md border border-gray-200 cursor-pointer hover:bg-gray-50"
                            wire:click="selectItem({{ $item->id }})"
                        >
                            <div class="flex items-center space-x-4">
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-md">
                                @endif
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $item->name }}</h3>
                                    <p class="text-sm text-gray-600">SKU: {{ $item->sku }}</p>
                                    <p class="text-sm text-gray-600">Brand: {{ $item->brand }}</p>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
    
            <!-- Right Section: Product Details -->
            <div class="w-2/4">
                @if($selectedItem)
                    <div class="bg-white p-6 shadow rounded-md">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ $selectedItem->name }}</h2>
                        <div class="flex space-x-6">
                            @if($selectedItem->image)
                                <img src="{{ asset('storage/'.$selectedItem->image) }}" alt="{{ $selectedItem->name }}" class="w-40 h-40 object-cover rounded-md">
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">SKU: {{ $selectedItem->sku }}</p>
                                <p class="text-sm text-gray-600">Brand: {{ $selectedItem->brand }}</p>
                                <p class="text-sm text-gray-600">Type: {{ $selectedItem->type }}</p>
                                <p class="text-sm text-gray-600">Cost: ${{ $selectedItem->cost }}</p>
                                <p class="text-sm text-gray-600">Price: ${{ $selectedItem->price }}</p>
                                <p class="text-sm text-gray-600">Quantity: {{ $selectedItem->quantity }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex justify-center items-center h-full">
                        <p class="text-gray-600">Select a product to see the details.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Modal Form for Adding Item -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-4 text-gray-600">Add New Item</h3>
                <div class="space-y-4">
                    <input type="text" wire:model="newItem.sku" placeholder="SKU" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="text" wire:model="newItem.name" placeholder="Name" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="number" wire:model="newItem.cost" placeholder="Cost" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="number" wire:model="newItem.price" placeholder="Price" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="text" wire:model="newItem.type" placeholder="Type" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="text" wire:model="newItem.brand" placeholder="Brand" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="number" wire:model="newItem.quantity" placeholder="Quantity" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="file" wire:model="image" class="w-full p-2 border border-gray-300 rounded-md">
                    <div class="flex justify-end mt-4">
                        <button wire:click="addItem" class="px-4 py-2 bg-blue-600 text-white rounded-md">Save</button>
                        <button wire:click="toggleModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md ml-2">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
