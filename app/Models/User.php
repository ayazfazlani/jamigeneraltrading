<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;


    protected $guarded = [];
    // Relationship with Team
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }


    // Check if user is a Super Admin
    public function isSuperAdmin()
    {
        return $this->hasRole('super admin');
    }

    // Check if user is a Team Admin
    public function isTeamAdmin()
    {
        return $this->hasRole('team admin');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_team_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Add relationship for current team
    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    // Helper method to switch current team
    public function switchTeam($teamId)
    {
        // Verify user belongs to this team
        if (!$this->teams()->where('team_id', $teamId)->exists()) {
            return false;
        }

        $this->current_team_id = $teamId;
        $this->save();

        // Clear any cached permissions
        $this->forgetCachedPermissions();

        return true;
    }

    // Get all teams user has access to
    public function accessibleTeams()
    {
        if ($this->hasRole('super admin')) {
            return Team::all();
        }

        return $this->teams;
    }
}