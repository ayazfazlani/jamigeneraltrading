<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\User;
use Livewire\Component;
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
        if (!auth()->user()->hasRole('super admin')) {
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
            'owner_id' => auth()->id()
        ]);

        session()->flash('status', 'Team created successfully!');
        $this->reset(['teamName', 'teamDescription']);
        $this->loadData();
    }
    public function deleteTeam($teamId)
    {
        $team = Team::findOrFail($teamId);

        // Ensure the user is authorized to delete the team
        if ($team->owner_id !== auth()->id() && !auth()->user()->hasRole('super admin')) {
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
    public function addUserToTeam()
    {
        $this->validate([
            'selectedUsers' => 'required|exists:users,id',
            'selectedTeam' => 'required|exists:teams,id'
        ]);

        // Find user and team
        $user = User::findOrFail($this->selectedUsers);
        $team = Team::findOrFail($this->selectedTeam);

        // Assign user to team
        $user->team_id = $team->id;
        $user->save();

        session()->flash('status', "User {$user->name} added to team {$team->name}");

        // Reset and reload
        $this->reset(['selectedUsers', 'selectedTeam']);
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
