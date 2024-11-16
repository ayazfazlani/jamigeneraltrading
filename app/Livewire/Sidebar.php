<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $isSidebarOpen = false;  // Define the public property here
    public $expandedSection = null;

    // Method to toggle the sidebar open/close
    public function toggleSidebar()
    {
        $this->isSidebarOpen = !$this->isSidebarOpen;  // Toggle the sidebar
    }

    public function render()
    {

        return view('livewire.sidebar');
    }
}
