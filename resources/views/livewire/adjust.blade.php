<div>
    <div class="p-6 z-0 flex-1 bg-white overflow-y-auto h-screen max-sm:flex-wrap">
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

        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Adjust Stock</h1>
            <button wire:click="fetchItems" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md">
                Refresh
            </button>
        </div>

        <!-- Search and Date Filter -->
        <div class=" items-center gap-4 my-6 flex flex-wrap md:flex">
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" 
                    placeholder="Search items by name or SKU..."
                    class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full flex justify-between md:justify-end md:flex-1 items-center gap-2 flex-wrap">
                <input type="date" wire:model.live="dateRange.start" 
                    class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 max-sm:w-36">
                <span class="text-gray-500">to</span>
                <input type="date" wire:model.live="dateRange.end" 
                    class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 max-sm:w-36">
            </div>
        </div>

        <hr class="my-4" />

        <div class="flex flex-wrap gap-6 sm:flex-nowrap">
            <div class="w-full sm:flex-1 border p-4 rounded-lg shadow-sm h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Select Items</h2>
                    @role('viewer')
                    @else
                    <button wire:click="openModal" class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded">
                        + Add Item
                    </button>
                    @endrole
                </div>
                <hr class="mb-4" />

                @if($loading)
                    <p>Loading items...</p>
                @else
                    <ul class="space-y-2">
                        @foreach($items as $item)
                            {{-- <li class="flex justify-between items-center p-2 border border-gray-200 rounded-md">
                                <div class="flex items-center">
                                    @if($item->image)
                                        <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover mr-4" />
                                    @endif
                                    <span class="text-gray-900">{{ $item->name }}</span>
                                </div>
                                <span class="text-gray-900">Quantity: {{ $item->quantity }}</span>
                                <div class="flex gap-2">
                                    @role('viewer')
                                    @else
                                        <button wire:click="openModal({{ $item->id }})" class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">
                                            <i class="fa fa-edit"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button wire:click.prevent="deleteItem({{ $item->id }})" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">
                                            <i class="fa fa-trash-alt"></i>
                                            <span>Delete</span>
                                        </button>
                                    @endrole
                                </div>
                            </li> --}}

                            <li wire:key="item-{{ $item->id }}" class="flex  items-center justify-between gap-2 p-2 border border-gray-200 rounded-md sm:flex-nowrap">
                                <div class="flex items-center flex-1 min-w-[200px]">
                                    @if($item->image)
                                        <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" 
                                             class="w-12 h-12 sm:w-16 sm:h-16 object-cover mr-2 sm:mr-4">
                                    @endif
                                    <span class="text-gray-900 text-sm sm:text-base">{{ $item->name }}</span>
                                </div>
                            
                                <!-- Quantity (hidden on mobile) -->
                                
                               <div class="flex-1">
                                <span class="text-gray-900 text-sm sm:text-base">
                                    <span class="max-sm:hidden">
                                        Quantity:
                                    </span>
                                     <span> {{ $item->quantity }}</span>
                                </span>
                               </div>
                            
                                <div class="flex gap-2">
                                    @role('viewer')
                                    @else
                                        <!-- Edit Button -->
                                        <button wire:click="openModal({{ $item->id }})" 
                                                class="bg-blue-500 text-white p-1 sm:py-1 sm:px-3 rounded hover:bg-blue-600 transition-colors"
                                                title="Edit">
                                            <i class="fa fa-edit text-sm sm:text-base"></i>
                                            <span class="max-sm:hidden ml-1">Edit</span>
                                        </button>
                            
                                        <!-- Delete Button -->
                                        <button wire:click.prevent="deleteItem({{ $item->id }})" 
                                                class="bg-red-500 text-white p-1 sm:py-1 sm:px-3 rounded hover:bg-red-600 transition-colors"
                                                title="Delete">
                                            <i class="fa fa-trash-alt text-sm sm:text-base"></i>
                                            <span class="max-sm:hidden ml-1">Delete</span>
                                        </button>
                                    @endrole
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="w-full sm:flex-1 border p-4 rounded-lg shadow-sm h-[80vh] overflow-y-auto">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Adjust Quantity</h2>
                <hr class="mb-4" />

                @if(count($selectedItems) === 0)
                    <p class="text-gray-900 italic">Please select an item to adjust quantity</p>
                @endif
            </div>
        </div>

     
        @if($isModalOpen)
        {{-- <div class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">
                    {{ $isEditing ? 'Edit Item' : 'Add New Item' }}
                </h3>
                <div class="space-y-4">
                    <input type="text" wire:model="newItem.sku" placeholder="SKU" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="text" wire:model="newItem.name" placeholder="Name" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="number" wire:model="newItem.cost" placeholder="Cost" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="number" wire:model="newItem.price" placeholder="Price" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="text" wire:model="newItem.type" placeholder="Type" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="text" wire:model="newItem.brand" placeholder="Brand" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="number" wire:model="newItem.quantity" placeholder="Quantity" class="w-full p-2 border border-gray-300 rounded-md">
                    <input type="file" wire:model="newItem.image" class="w-full p-2 border border-gray-300 rounded-md">
                    <div class="flex justify-end mt-4">
                        <button wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md mr-2">
                            Close
                        </button>
                        <button wire:click="saveItem" class="px-4 py-2 bg-green-500 text-white rounded-md">
                            {{ $isEditing ? 'Save Changes' : 'Add Item' }}
                        </button>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">
                    {{ $isEditing ? 'Edit Item' : 'Add New Item' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <input type="text" wire:model="newItem.sku" placeholder="SKU" 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('newItem.sku') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
        
                    <div>
                        <input type="text" wire:model="newItem.name" placeholder="Name" 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('newItem.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
        
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="number" wire:model="newItem.cost" placeholder="Cost" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('newItem.cost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <input type="number" wire:model="newItem.price" placeholder="Price" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('newItem.price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
        
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="text" wire:model="newItem.type" placeholder="Type" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('newItem.type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <input type="text" wire:model="newItem.brand" placeholder="Brand" 
                                class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('newItem.brand') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
        
                    <div>
                        <input type="number" wire:model="newItem.quantity" placeholder="Quantity" 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('newItem.quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
        
                    <div>
                        <input type="file" wire:model="newItem.image" 
                            class="w-full p-2 border border-gray-300 rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('newItem.image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
        
                    <div class="flex justify-end mt-4">
                        <button wire:click="closeModal" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Close</button>
                        <button wire:click="saveItem" 
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 ml-2">
                            {{ $isEditing ? 'Save Changes' : 'Add Item' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        @endif
    </div>
</div>