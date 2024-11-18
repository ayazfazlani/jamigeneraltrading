<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h3 class="text-2xl font-bold mb-4">Manage Roles for {{ $user->name }}</h3>
        <div class="mb-4">
            <input type="text" wire:model="selectedRole" placeholder="Enter role name" class="border rounded w-full p-2">
            <button wire:click="addRole" class="bg-blue-500 text-white rounded w-full py-2 mt-2">Assign Role</button>
        </div>
        <div class="mb-4">
            <input type="text" wire:model="newRoleName" placeholder="New role name" class="border rounded w-full p-2">
            <button wire:click="createRole" class="bg-green-500 text-white rounded w-full py-2 mt-2">Create Role</button>
        </div>
        <ul class="list-disc pl-5">
            @foreach($userRoles as $role)
                <li class="flex justify-between items-center">
                    <span>{{ $role }}</span>
                    <button wire:click="removeRole('{{ $role }}')" class="bg-red-500 text-white rounded px-2">Remove</button>
                </li>
            @endforeach
        </ul>
    </div>
</div>