<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Mail\InviteUserMail;
use App\Models\InvitationToken;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    public $email;
    public $selectedRoles = [];
    public $users;
    public $availableRoles;
    public $showDeleteModal = false;
    public $userToDelete;

    protected $rules = [
        'email' => 'required|email|unique:users,email',
        'selectedRoles.*' => 'nullable|exists:roles,name'
    ];

    public function mount()
    {
        $this->users = User::with('roles')->get();
        $this->availableRoles = Role::all();
    }

    public function sendInvitation()
    {
        $this->validate();

        // Check if email already exists
        if (User::where('email', $this->email)->exists()) {
            session()->flash('status', 'Email already registered.');
            return;
        }

        // Expire old invitations for the same email
        InvitationToken::where('email', $this->email)
            ->where('expires_at', '>', now())
            ->delete();

        // Generate a new token
        $token = Str::random(32);
        $expirationTime = config('invitation.expiration_time', 24); // Fetch configurable expiration time, default to 24 hours

        // Store the invitation
        InvitationToken::create([
            'email' => $this->email,
            'token' => $token,
            'expires_at' => now()->addHours($expirationTime),
        ]);

        // Send the invitation email
        try {
            Mail::to($this->email)->send(new InviteUserMail($token));
            session()->flash('status', 'Invitation sent successfully!');
        } catch (\Exception $e) {
            // Handle mail sending failure
            session()->flash('error', 'Failed to send the invitation. Please try again.');
            return;
        }

        // Reset form input
        $this->reset('email');
        $this->mount();
    }


    public function assignRole($userId)
    {
        if (!isset($this->selectedRoles[$userId]) || empty($this->selectedRoles[$userId])) {
            session()->flash('status', 'Please select a role first.');
            return;
        }

        $user = User::findOrFail($userId);

        // Remove all existing roles and assign the new one
        $user->syncRoles([$this->selectedRoles[$userId]]);

        session()->flash('status', 'Role assigned successfully!');
        $this->selectedRoles = [];
        $this->mount();
    }

    public function removeAllRoles($userId)
    {
        $user = User::findOrFail($userId);
        $user->syncRoles([]); // Using syncRoles with empty array instead of detach

        session()->flash('status', 'All roles removed successfully!');
        $this->mount();
    }

    public function confirmDelete($userId)
    {
        $this->userToDelete = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->userToDelete) {
            $user = User::findOrFail($this->userToDelete);
            $user->delete();

            session()->flash('status', 'User deleted successfully!');
            $this->showDeleteModal = false;
            $this->userToDelete = null;
            $this->mount();
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    public function render()
    {
        return view('livewire.user-management');
    }
}
