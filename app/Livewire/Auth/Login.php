<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; // Ensure you import Auth

class Login extends Component
{
    public $email; // Add properties for email and password
    public $password;

    public function login() // Create a login method
    {
        $this->validate([ // Use $this->validate() for validation
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            // Redirect to intended page
            return redirect()->intended('/')->with('status', 'Login successful!');
        }

        // If login fails, redirect back with an error
        return redirect()->back()->with('error', 'Invalid credentials.');
    }

    /**
     * Handle logout request.
     */
    public function logout()
    {
        Auth::logout();
        session()->forget('current_team_id'); // Clear the current team ID
        return redirect('/')->with('status', 'Logged out successfully.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}