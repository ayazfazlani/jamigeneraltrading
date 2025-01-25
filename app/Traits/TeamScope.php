<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait TeamScope
{
  protected static function bootTeamScope()
  {
    static::addGlobalScope('team', function (Builder $builder) {
      $user = Auth::user();

      if (!$user) {
        return;
      }

      // Super admin can see all data
      if ($user->hasRole('super admin')) {
        return;
      }

      $table = $builder->getModel()->getTable();
      $hasUserIdColumn = Schema::hasColumn($table, 'user_id');

      // If user has current team, show team data
      if ($user->current_team_id) {
        if ($hasUserIdColumn) {
          $builder->where(function ($query) use ($user) {
            $query->where('team_id', $user->current_team_id)
              ->orWhere('user_id', $user->id);
          });
        } else {
          $builder->where('team_id', $user->current_team_id);
        }
        return;
      }

      // Otherwise show only user's data if the column exists
      if ($hasUserIdColumn) {
        $builder->where('user_id', $user->id);
      } else {
        $builder->whereNull('team_id');
      }
    });

    // Auto-set team_id and user_id on model creation
    static::creating(function ($model) {
      if (!isset($model->team_id) && Auth::check() && Auth::user()->current_team_id) {
        $model->team_id = Auth::user()->current_team_id;
      }

      $table = $model->getTable();
      if (Schema::hasColumn($table, 'user_id') && !isset($model->user_id) && Auth::check()) {
        $model->user_id = Auth::id();
      }
    });
  }

  // Helper method to check if a model belongs to a team
  public function belongsToTeam($teamId)
  {
    return $this->team_id === $teamId;
  }
}
