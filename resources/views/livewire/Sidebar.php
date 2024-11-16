<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
  public $isSidebarOpen = false; // Track if the sidebar is open
  public $expandedSection = null; // Track which section is expanded

  // Toggle the sidebar open/close
  public function toggleSidebar()
  {
    $this->isSidebarOpen = !$this->isSidebarOpen;
  }

  // Toggle the expansion of a section
  public function toggleSection($sectionName)
  {
    if ($this->expandedSection === $sectionName) {
      $this->expandedSection = null; // Collapse the section
    } else {
      $this->expandedSection = $sectionName; // Expand the section
    }
  }

  public function render()
  {
    // The data for the sidebar can come from a property or dynamic content
    $sections = [
      // Example structure for sections
      [
        'name' => 'Dashboard',
        'path' => '/dashboard',
        'icon' => 'heroicon-o-home',
        'subItems' => [
          [
            'name' => 'Overview',
            'path' => '/dashboard/overview',
            'icon' => 'heroicon-o-clipboard',
          ],
          [
            'name' => 'Stats',
            'path' => '/dashboard/stats',
            'icon' => 'heroicon-o-chart-bar',
          ],
        ]
      ],
      // Add more sections as needed
    ];

    return view('livewire.sidebar', compact('sections'));
  }
}
