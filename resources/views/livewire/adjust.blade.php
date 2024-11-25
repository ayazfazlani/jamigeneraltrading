<div>
    <div class="p-6 z-0 flex-1 bg-white overflow-y-auto h-screen">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Adjust Stock</h1>
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-900 py-2 px-4 rounded">Reset</button>
        </div>

        <hr class="my-4" />

        <div class="flex gap-8">
            <div class="flex-1 border p-4 rounded-lg shadow-sm h-[80vh] overflow-y-auto">
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
                            <li class="flex justify-between items-center p-2 border border-gray-200 rounded-md">
                                <div class="flex items-center">
                                    @if($item->image)
                                        <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover mr-4" />
                                    @endif
                                    <span class="text-gray-900">{{ $item->name }}</span>
                                </div>
                                <span class="text-gray-900">Quantity: {{ $item->quantity }}</span>
                                <div class="flex gap-2">
                                    {{-- @canNot('viewer')
                                    <button wire:click="openModal({{ $item->id }})" class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">
                                        <i class="fa fa-edit"> </i>
                                    </button>
                                    <button wire:click.prevent="deleteItem({{ $item->id }})" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">
                                        <i class="fa fa-trash-alt"> </i>
                                    </button>
                                    @endcanNot --}}

                                    @role('viewer')
    <!-- Viewer users won't see the buttons -->
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
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="flex-1 border p-4 rounded-lg shadow-sm h-[80vh] overflow-y-auto">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Adjust Quantity</h2>
                <hr class="mb-4" />

                @if(count($selectedItems) === 0)
                    <p class="text-gray-900 italic">Please select an item to adjust quantity</p>
                @endif
            </div>
        </div>

        @if($isModalOpen)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-10 flex justify-center items-center">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        {{ $isEditing ? 'Edit Item' : 'Add New Item' }}
                    </h2>
                    <form wire:submit.prevent="saveItem">
                        <input wire:model="newItem.sku" type="text" class="w-full p-2 mb-2 border placeholder-gray-700 text-gray-800 border-gray-300 rounded-md" placeholder="SKU" />
                        <input wire:model="newItem.name" type="text" class="w-full p-2 mb-2 border placeholder-gray-700 text-gray-800 border-gray-300 rounded-md" placeholder="Name" />
                        <input wire:model="newItem.cost" type="number" class="w-full p-2 mb-2 border placeholder-gray-700 text-gray-800 border-gray-300 rounded-md" placeholder="Cost" />
                        <input wire:model="newItem.price" type="number" class="w-full p-2 mb-2 border placeholder-gray-700 text-gray-800 border-gray-300 rounded-md" placeholder="Price" />
                        <input wire:model="newItem.type" type="text" class="w-full p-2 mb-2 border placeholder-gray-700 text-gray-800 border-gray-300 rounded-md" placeholder="Type" />
                        <input wire:model="newItem.brand" type="text" class="w-full p-2 mb-2 border placeholder-gray-700 text-gray-800 border-gray-300 rounded-md" placeholder="Brand" />
                        <input wire:model="newItem.quantity" type="number" class="w-full p-2 mb-2 border placeholder-gray-700 text-gray-800 border-gray-300 rounded-md" placeholder="Quantity" />
                        
                        <div class="mb-4">
                            <label for="image" class="text-sm text-gray-700">Image</label>
                            <input wire:model="newItem.image" type="file" class="block w-full py-2 px-3 mt-1 border border-gray-300 rounded-md" />
                        </div>

                        <div class="flex justify-between items-center">
                            <button type="button" wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-900 py-2 px-4 rounded">
                                Close
                            </button>
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                                {{ $isEditing ? 'Save Changes' : 'Add Item' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
