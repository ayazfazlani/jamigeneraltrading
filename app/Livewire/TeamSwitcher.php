<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TeamSwitcher extends Component
{
  public $currentTeamId;
  public $teams = [];

  protected $listeners = ['refreshTeams' => 'loadTeams'];

  public function mount()
  {
    $this->loadTeams();
  }

  // public function loadTeams()
  // {
  //   $this->teams = Auth::user()->accessibleTeams();
  //   $this->currentTeamId = session('current_team_id', Auth::user()->current_team_id);
  // }

  // public function switchTeam()
  // {
  //   if (!$this->teams->contains('id', $this->currentTeamId)) {
  //     session()->flash('error', 'Invalid team selection');
  //     return;
  //   }

  //   try {
  //     // Update session and user's current team
  //     session(['current_team_id' => $this->currentTeamId]);
  //     Auth::user()->update(['current_team_id' => $this->currentTeamId]);

  //     // Flash success message
  //     session()->flash('status', 'Team switched successfully!');

  //     // Emit event for other components
  //     $this->dispatch('teamChanged', $this->currentTeamId);

  //     // Refresh the page
  //     return redirect(request()->header('Referer'));
  //   } catch (\Exception $e) {
  //     session()->flash('error', 'Error switching teams');
  //   }
  // }
  public function loadTeams()
  {
    // Get teams from pivot relationship
    $this->teams = Auth::user()->teams;

    // Set current team: session → user's current → first team
    $this->currentTeamId = session(
      'current_team_id',
      Auth::user()->current_team_id ?? optional($this->teams->first())->id
    );
  }

  public function switchTeam()
  {
    // Validate team belongs to user
    if (!$this->teams->contains('id', $this->currentTeamId)) {
      abort(403, 'Unauthorized team selection');
    }

    // Update session and database
    session(['current_team_id' => $this->currentTeamId]);
    Auth::user()->update(['current_team_id' => $this->currentTeamId]);
    session()->flash('status', 'Team switched successfully!');
    // Force refresh of all components
    return redirect()->route("home"); // Adjust to your route
  }
  public function render()
  {
    return view('livewire.team-switcher');
  }
}
