<?php

namespace App\Http\Controllers;

use App\Models\InvitationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show the registration form with the token.
     */
    public function showRegistrationForm(Request $request)
    {
        $token = $request->query('token');

        // Verify the token from the database
        $invitation = InvitationToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect('/')->with('error', 'Invalid or expired invitation link.');
        }

        return view('auth.register', ['email' => $invitation->email, 'token' => $token]);
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
        ]);

        // Validate the token again
        $invitation = InvitationToken::where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation || $invitation->email !== $request->email) {
            return redirect('/')->with('error', 'Invalid or expired invitation link.');
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Delete the token after successful registration
        $invitation->delete();

        // Log the user in
        Auth::login($user);

        return redirect()->back()->with('status', 'Registration successful!');
    }
}
