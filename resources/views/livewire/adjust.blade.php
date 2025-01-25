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
    <div class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto flex items-center justify-center z-50">
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
    </div>
@endif

    </div>
</div>
