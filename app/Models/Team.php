<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'description'
    ];

    // Relationship with User (Owner)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Relationship with Users (Team Members)
    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    // Validate team membership requirements
    public function validateTeamMembership()
    {
        $adminCount = $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'team admin');
        })->count();

        $memberCount = $this->users()->count();

        return $adminCount >= 1 && $memberCount >= 2;
    }

    // Method to add a user to the team
    public function addUser(User $user)
    {
        return $this->users()->save($user);
    }

    // Method to remove a user from the team
    public function removeUser(User $user)
    {
        $user->team_id = null;
        return $user->save();
    }
}
