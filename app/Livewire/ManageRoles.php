<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class ManageRoles extends Component
{
    public $user;
    public $allRoles = [];
    public $selectedRole;
    public $newRoleName;

    public function mount($userId)
    {
        $this->user = User::find($userId);

        if (!$this->user) {
            session()->flash('error', 'User not found.');
            return redirect()->route('home'); // Redirect to a safe route
        }

        $this->allRoles = Role::pluck('name')->toArray();
    }

    public function addRole()
    {
        if ($this->selectedRole) {
            $this->user->assignRole($this->selectedRole);
            $this->selectedRole = ''; // Clear input after assigning
        }
    }

    public function removeRole($roleName)
    {
        $this->user->removeRole($roleName);
    }

    public function createRole()
    {
        if ($this->newRoleName) {
            Role::create(['name' => $this->newRoleName]);
            $this->newRoleName = ''; // Clear input after creating
            $this->allRoles = Role::pluck('name')->toArray(); // Refresh roles
        }
    }

    public function render()
    {
        return view('livewire.manage-roles', [
            'userRoles' => $this->user->roles->pluck('name')->toArray()
        ]);
    }
}
