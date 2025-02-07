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
        if (!Auth::user()->hasRole('super admin')) {
            abort(403, 'Unauthorized access');
        }

        $this->loadData();
    }

    protected function loadData()
    {
        $this->teams = Team::with(['owner', 'users'])->get();

        // Get users not in any team using pivot relationship
        $this->availableUsers = User::whereDoesntHave('teams')->get();

        // Get viewer role users
        $viewerUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'viewer');
        })->get();
        // dd($viewerUsers);
        $this->availableUsers = $this->availableUsers->merge($viewerUsers);
        $this->availableRoles = Role::all();
    }

    public function createTeam()
    {
        $this->validate([
            'teamName' => 'required|unique:teams,name',
            'teamDescription' => 'nullable|string|max:255'
        ]);

        Team::create([
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

        if ($team->owner_id !== Auth::id() && !Auth::user()->hasRole('super admin')) {
            session()->flash('status', 'Unauthorized to delete this team.');
            return;
        }

        // Detach all users from team through pivot
        $team->users()->detach();
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

        $user = User::findOrFail($this->selectedUsers);
        $team = Team::findOrFail($this->selectedTeam);

        if ($team->users()->where('user_id', $user->id)->exists()) {
            session()->flash('status', 'User is already a member of this team.');
            return;
        }

        // Attach user to team through pivot
        $team->users()->attach($user->id);

        // Set current team if not set
        if (!$user->current_team_id) {
            $user->update(['current_team_id' => $team->id]);
        }

        session()->flash('status', "User {$user->name} added to team {$team->name}");
        $this->loadData();
    }

    public function removeUserFromTeam($userId, $teamId)
    {
        $user = User::findOrFail($userId);
        $team = Team::findOrFail($teamId);

        // Detach user from team through pivot
        $team->users()->detach($userId);

        // Reset current team if it was this team
        if ($user->current_team_id == $teamId) {
            $newTeam = $user->teams()->first();
            $user->update(['current_team_id' => $newTeam->id ?? null]);
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
        $user->syncRoles([$this->selectedRole]);

        session()->flash('status', "Role changed to {$this->selectedRole} for {$user->name}");
        $this->reset(['selectedUser', 'selectedRole']);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.team-management');
    }
}
