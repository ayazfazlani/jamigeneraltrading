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

    public function mount($userId)
    {
        $this->user = User::find($userId);
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

    public function render()
    {
        return view('livewire.manage-roles', [
            'userRoles' => $this->user->roles->pluck('name')->toArray()
        ]);
    }
}
