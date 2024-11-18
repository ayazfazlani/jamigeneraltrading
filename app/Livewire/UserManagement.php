<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Mail\InviteUserMail;
use App\Models\InvitationToken;
use Illuminate\Support\Facades\Mail;

class UserManagement extends Component
{
    public $email;
    public $role;
    public $users;

    public function mount()
    {
        $this->users = User::all();
    }

    public function sendInvitation()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $token = Str::random(32);
        InvitationToken::create([
            'email' => $this->email,
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        Mail::to($this->email)->send(new InviteUserMail($token));

        session()->flash('status', 'Invitation sent successfully!');
        $this->email = ''; // Clear the input
        $this->mount(); // Refresh the user list
    }

    public function assignRole($userId)
    {
        $this->validate([
            'role' => 'required|string',
        ]);

        $user = User::findOrFail($userId);
        $user->assignRole($this->role);

        session()->flash('status', 'Role assigned successfully!');
        $this->mount(); // Refresh the user list
    }

    public function render()
    {
        return view('livewire.user-management');
    }
}
