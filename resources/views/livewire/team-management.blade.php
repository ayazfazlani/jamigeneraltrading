<div class="p-6 max-w-7xl mx-auto bg-white rounded-lg shadow-md">
    @livewire('user-management')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Team Management</h1>

    @if (session()->has('status'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('status') }}
        </div>
    @endif
   
    <!-- Create Team Section -->
    <div class="mb-8 bg-gray-50 p-6 rounded-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Create New Team</h2>
        <form wire:submit.prevent="createTeam" class="space-y-4">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="teamName" class="block text-sm font-medium text-gray-700 mb-1">Team Name</label>
                    <input type="text" 
                           wire:model="teamName" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Enter team name"
                           required>
                </div>
                <div>
                    <label for="teamDescription" class="block text-sm font-medium text-gray-700 mb-1">Team Description</label>
                    <textarea 
                        wire:model="teamDescription" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Enter team description"
                        rows="3"></textarea>
                </div>
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Create Team
            </button>
        </form>
    </div>

    <!-- Add User to Team Section -->
    <div class="mb-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Add User to Team</h2>
        <div class="flex space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                <select 
                    wire:model="selectedUsers" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a User</option>
                    @foreach($availableUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Team</label>
                <select 
                    wire:model="selectedTeam" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a Team</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button 
                    wire:click="addUserToTeam"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Add User
                </button>
            </div>
        </div>
    </div>

    <!-- Change User Role Section -->
    <div class="mb-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Change User Role</h2>
        <div class="flex space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                <select 
                    wire:model="selectedUser" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a User</option>
                    @foreach($teams as $team)
                        @foreach($team->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $team->name }})</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Role</label>
                <select 
                    wire:model="selectedRole" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a Role</option>
                    @foreach($availableRoles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button 
                    wire:click="changeUserRole"
                    class="px-6 py-2 bg-yellow-400 text-white rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2">
                    Change Role
                </button>
            </div>
        </div>
    </div>

    <!-- Teams List Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Existing Teams</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($teams as $team)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $team->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">Owner: {{ $team->owner->name }}</p>
                    <div class="border-t pt-2">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Team Members</h4>
                        <ul class="space-y-1">
                            @foreach($team->users as $user)
                                <li class="flex justify-between text-sm">
                                    <span>{{ $user->name }}</span>
                                    <span class="text-xs text-gray-500">
                                        {{ $user->getRoleNames()->first() ?? 'No Role' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button 
                            wire:click="deleteTeam({{ $team->id }})"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Delete Team
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
</div>