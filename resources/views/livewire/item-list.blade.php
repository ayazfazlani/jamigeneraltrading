<div>
    <div class="p-6 flex-1 bg-white min-h-screen overflow-y-auto">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-4 max-sm:flex-wrap max-sm:gap-2">
            <h1 class="text-2xl font-semibold">Items</h1>
            <div class="flex gap-2 max-sm:w-full max-sm:justify-between">
                @role('viewer')
                @else
                <button
                    wire:click="toggleImportModal"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Import</span>
                </button>
                <button
                    wire:click="toggleModal"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Item</span>
                </button>
                @endrole
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="flex flex-wrap  items-center gap-4 mb-6 max-sm:justify-between">
            <div class="relative w-full md:flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search items..."
                    class="w-full p-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
           <div class="w-full flex justify-end md:flex-1 md:justify-normal">
            <button
            wire:click="$toggle('inStockOnly')"
            class="justify-end px-4 py-2 border rounded-md transition-colors {{ $inStockOnly ? 'bg-green-500 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
        >
            In Stock Only
        </button>
           </div>
        </div>

        <!-- Main Content -->
        <div class="flex gap-6 max-sm:flex-wrap">
            <!-- Items List -->
            <div class="w-full md:w-1/2 md:pr-3">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-700">Item List</h2>
                    </div>
                    <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
                        @forelse($items as $item)
                            <div
                                wire:key="item-{{ $item->id }}"
                                wire:click="selectItem({{ $item->id }})"
                                class="p-4 bg-white rounded-md border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors {{ $selectedItem && $selectedItem->id === $item->id ? 'ring-2 ring-blue-400' : '' }}"
                            >
                                <div class="flex items-center gap-4">
                                    @if($item->image)
                                    <img 
                                        src="{{ asset('storage/'.$item->image) }}" 
                                        alt="{{ $item->name }}"
                                        class="w-16 h-16 object-cover rounded-md"
                                    >
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-800">{{ $item->name }}</h3>
                                        <div class="text-sm text-gray-600 mt-1">
                                            <p>SKU: {{ $item->sku }}</p>
                                            <p>Brand: {{ $item->brand }}</p>
                                            <p class="mt-1">
                                                Quantity: 
                                                <span class="font-medium {{ $item->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $item->quantity }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">
                                No items found matching your criteria
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Item Details -->
            <div class="w-full md:w-1/2 md:pr-3">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm h-full">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-700">Item Details</h2>
                    </div>
                    <div class="p-4 h-[70vh] overflow-y-auto">
                        @if($selectedItem)
                            <div class="space-y-4">
                                @if($selectedItem->image)
                                <img 
                                    src="{{ asset('storage/'.$selectedItem->image) }}" 
                                    alt="{{ $selectedItem->name }}"
                                    class="w-full h-48 object-cover rounded-md mb-4"
                                >
                                @endif
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Name</label>
                                        <p class="text-gray-900">{{ $selectedItem->name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">SKU</label>
                                        <p class="text-gray-900">{{ $selectedItem->sku }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Brand</label>
                                        <p class="text-gray-900">{{ $selectedItem->brand }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Type</label>
                                        <p class="text-gray-900">{{ $selectedItem->type }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Cost</label>
                                        <p class="text-gray-900">${{ number_format($selectedItem->cost, 2) }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Price</label>
                                        <p class="text-gray-900">${{ number_format($selectedItem->price, 2) }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Quantity</label>
                                        <p class="text-gray-900 {{ $selectedItem->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $selectedItem->quantity }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="h-full flex items-center justify-center text-gray-500">
                                Select an item to view details
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        @if($isModalOpen)
            {{-- <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white w-full max-w-md rounded-lg shadow-xl">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold">Add New Item</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <input type="text" wire:model="newItem.sku" placeholder="SKU" class="w-full p-2 border rounded-md">
                        <input type="text" wire:model="newItem.name" placeholder="Name" class="w-full p-2 border rounded-md">
                        <input type="number" wire:model="newItem.cost" placeholder="Cost" class="w-full p-2 border rounded-md">
                        <input type="number" wire:model="newItem.price" placeholder="Price" class="w-full p-2 border rounded-md">
                        <input type="text" wire:model="newItem.type" placeholder="Type" class="w-full p-2 border rounded-md">
                        <input type="text" wire:model="newItem.brand" placeholder="Brand" class="w-full p-2 border rounded-md">
                        <input type="number" wire:model="newItem.quantity" placeholder="Quantity" class="w-full p-2 border rounded-md">
                        <input type="file" wire:model="image" class="w-full p-2 border rounded-md">
                    </div>
                    <div class="p-6 border-t border-gray-200 flex justify-end gap-2">
                        <button wire:click="toggleModal" class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-md">Cancel</button>
                        <button wire:click="addItem" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">Save Item</button>
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
                        <button wire:click="toggleModal" 
                            class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-md">Cancel</button>
                        <button wire:click="addItem" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Item</button>
                    </div>
                </div>
            </div>
            
            
        @endif

        @if($isImportModalOpen)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white w-full max-w-md rounded-lg shadow-xl">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold">Import Items</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <input type="file" wire:model.live="importFile" class="w-full p-2 border rounded-md">
                        @error('importFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="p-6 border-t border-gray-200 flex justify-end gap-2">
                        <button wire:click="toggleImportModal" class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-md">Cancel</button>
                        <button wire:click="importItems" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Import</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>