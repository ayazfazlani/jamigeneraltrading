<?php

namespace App\Http\Controllers;

use App\Mail\InviteUserMail;
use App\Models\InvitationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class InviteController extends Controller
{
    public function sendInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        // Generate a unique token
        $token = Str::random(32);


        // Store the token in the database
        InvitationToken::create([
            'email' => $request->email,
            'token' => $token,
            'expires_at' => now()->addMinutes(20),
            dd($request->email) // Token valid for 24 hours
        ]);




        // Send the invitation email
        Mail::to($request->email)->send(new InviteUserMail($token));

        return back()->with('status', 'Invitation sent successfully!');
    }
}
