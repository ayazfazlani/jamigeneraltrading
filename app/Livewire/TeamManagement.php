<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class TeamManagement extends Component
{
    // Team Creation
    public $teamName;
    public $teamDescription;

    // Add User to Team
    public $selectedUsers;
    public $selectedTeam;

    // Change User Role
    public $selectedUser;
    public $selectedRole;

    // Data Collections
    public $teams;
    public $availableUsers;
    public $availableRoles;

    public function mount()
    {
        // Ensure only super admin can access
        if (!Auth::user()->hasRole('super admin')) {
            abort(403, 'Unauthorized access');
        }

        $this->loadData();
    }


    protected function loadData()
    {
        // Load all teams with their users and owners
        $this->teams = Team::with(['owner', 'users'])->get();

        // Load users not assigned to any team
        $this->availableUsers = User::whereNull('team_id')->get();

        // Load "viewer" role users who can be assigned to multiple teams
        $viewerUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'viewer');
        })->get();

        // Merge both available users and viewer users
        $this->availableUsers = $this->availableUsers->merge($viewerUsers);

        // Load all available roles
        $this->availableRoles = Role::all();
    }

    public function createTeam()
    {
        $this->validate([
            'teamName' => 'required|unique:teams,name',
            'teamDescription' => 'nullable|string|max:255'
        ]);

        $team = Team::create([
            'name' => $this->teamName,
            'description' => $this->teamDescription,
            'owner_id' => Auth::id()
        ]);

        session()->flash('status', 'Team created successfully!');
        $this->reset(['teamName', 'teamDescription']);
        $this->loadData();
    }
    public function deleteTeam($teamId)
    {
        $team = Team::findOrFail($teamId);

        // Ensure the user is authorized to delete the team
        if ($team->owner_id !== Auth::id() && !Auth::user()->hasRole('super admin')) {
            session()->flash('status', 'Unauthorized to delete this team.');
            return;
        }

        // Unassign users from the team
        User::where('team_id', $team->id)->update(['team_id' => null]);

        // Delete the team
        $team->delete();

        session()->flash('status', "Team {$team->name} deleted successfully!");
        $this->loadData();
    }
    // public function addUserToTeam()
    // {
    //     $this->validate([
    //         'selectedUsers' => 'required|exists:users,id',
    //         'selectedTeam' => 'required|exists:teams,id'
    //     ]);

    //     // Find user and team
    //     $user = User::findOrFail($this->selectedUsers);
    //     $team = Team::findOrFail($this->selectedTeam);

    //     // Assign user to team
    //     $user->team_id = $team->id;
    //     $user->save();

    //     session()->flash('status', "User {$user->name} added to team {$team->name}");

    //     // Reset and reload
    //     $this->reset(['selectedUsers', 'selectedTeam']);
    //     $this->loadData();
    // }
    // app/Livewire/TeamManagement.php

    // public function addUserToTeam()
    // {
    //     $this->validate([
    //         'selectedUsers' => 'required|exists:users,id', // Ensure a single user is selected
    //         'selectedTeam' => 'required|exists:teams,id'
    //     ]);

    //     // Find user and team
    //     $user = User::findOrFail($this->selectedUsers);
    //     $team = Team::findOrFail($this->selectedTeam);

    //     // Attach user to team (this allows the user to be in multiple teams)
    //     $team->users()->attach($user->id);

    //     session()->flash('status', "User {$user->name} added to team {$team->name}");

    //     // Reset and reload
    //     // $this->reset(['selectedUsers', 'selectedTeam']);
    //     $this->loadData();
    // }

    public function addUserToTeam()
    {
        $this->validate([
            'selectedUsers' => 'required|exists:users,id',
            'selectedTeam' => 'required|exists:teams,id'
        ]);

        $user = User::findOrFail($this->selectedUsers);
        $team = Team::findOrFail($this->selectedTeam);

        // Check if the user is already a member of the team
        if ($team->users()->where('users.id', $user->id)->exists()) {
            session()->flash('status', 'User is already a member of this team.');
            return;
        }

        // Add user to team
        $team->users()->attach($user->id);

        // If this is the user's first team, set it as current team
        if ($user->current_team_id) {
            $user->current_team_id = $team->id;
            $user->team_id = $team->id;
            $user->save();
        }

        session()->flash('status', "User {$user->name} added to team {$team->name}");
        $this->loadData();
    }

    public function removeUserFromTeam($userId, $teamId)
    {
        $user = User::findOrFail($userId);
        $team = Team::findOrFail($teamId);
        $team->users()->detach($user->id);
        // Check if user is a viewer (uses many-to-many relationship)
        if ($user->hasRole('viewer')) {
            $team->users()->detach($user->id);
        } else {
            // For regular users, just set team_id to null
            $user->team_id = null;
            $user->save();
        }

        session()->flash('status', "User {$user->name} removed from team {$team->name}");
        $this->loadData();
    }

    public function changeUserRole()
    {
        $this->validate([
            'selectedUser' => 'required|exists:users,id',
            'selectedRole' => 'required|exists:roles,name'
        ]);

        $user = User::findOrFail($this->selectedUser);

        // Sync user roles
        $user->syncRoles([$this->selectedRole]);

        session()->flash('status', "Role changed to {$this->selectedRole} for {$user->name}");

        // Reset and reload
        $this->reset(['selectedUser', 'selectedRole']);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.team-management');
    }
}
