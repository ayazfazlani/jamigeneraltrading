<div>
    <h3>Manage Roles for {{ $user->name }}</h3>
    <div>
        <input type="text" wire:model="selectedRole" placeholder="Enter role name">
        <button wire:click="addRole">Assign Role</button>
    </div>
    <ul>
        @foreach($userRoles as $role)
            <li>
                {{ $role }}
                <button wire:click="removeRole('{{ $role }}')">Remove</button>
            </li>
        @endforeach
    </ul>
</div>
