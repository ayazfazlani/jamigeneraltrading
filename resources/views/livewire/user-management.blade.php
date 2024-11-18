<div>
    <h1>User Management</h1>

    @if (session()->has('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form wire:submit.prevent="sendInvitation">
        <div class="form-group">
            <label for="email">Invite User Email</label>
            <input type="email" wire:model="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Send Invitation</button>
    </form>

    <h2>All Users</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ implode(', ', $user->getRoleNames()->toArray()) }}</td>
                    <td>
                        <select wire:model="role">
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        <button wire:click="assignRole({{ $user->id }})" class="btn btn-primary">Assign Role</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>