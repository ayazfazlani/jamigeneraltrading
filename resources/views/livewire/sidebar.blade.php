<div>
    <nav class="sidebar z-10 border-r border-gray-300">
        <header>
            <div class="image-text">
                <span class="image">
                     {{-- <img src="icon.png" alt=""> --}}
                </span>

                <div class="text logo-text">
                    <span class="name">GENERAL</span>
                    <span class="profession">TRADING</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

                {{-- <li class="search-box">
                    <i class='bx bx-search icon'></i>
                    <input type="text" placeholder="Search...">
                </li> --}}

                <ul class="menu-links">
                    <li class="nav-link">
                        <a wire:navigate href="{{ route('items')}}">
                            <i class='bx bx-list-ol icon'></i>
                            <span class="text nav-text">Item List</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a wire:navigate href="{{ route('stock-in')}}">
                            <i class='bx bx-cart icon'></i>
                            <span class="text nav-text">Stock In</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a wire:navigate href="{{route('stock-out')}}">
                            <i class='bx bx-cart-alt icon'></i>
                            <span class="text nav-text">Stock Out</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a wire:navigate href="{{route('adjust')}}">
                            <i class='bx bx-adjust icon'></i>
                            <span class="text nav-text">Adjust</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a wire:navigate href="{{route('transactions')}}">
                            <i class='bx bx-credit-card icon'></i>
                            <span class="text nav-text">Transactions</span>
                        </a>
                    </li>

                    <li class="nav-link"> 
                        <a wire:navigate href="{{route('analytics')}}">
                            <i class='bx bx-bar-chart-square icon'></i>
                            <span class="text nav-text">Analytics</span>
                        </a>
                    </li>

                   
                  
                    <li class="nav-link">
                        <a wire:navigate href="{{route('summary')}}">
                            <i class='bx bx-file icon'></i>
                            <span class="text nav-text">Summary</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a wire:navigate href="{{route('dashboard')}}">
                            <i class='bx bx-grid-alt icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- <div class="bottom-content">
                <li class="">
                    <a href="#">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>

                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Dark mode</span>

                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
                
            </div> --}}
        </div>

    </nav>
</div>

@script
<script>
const body = document.querySelector('body'),
      sidebar = body.querySelector('nav'),
      toggle = body.querySelector(".toggle"),
      searchBtn = body.querySelector(".search-box"),
      modeSwitch = body.querySelector(".toggle-switch"),
      modeText = body.querySelector(".mode-text");

// Load sidebar state from localStorage
const sidebarState = localStorage.getItem('sidebarState');
if (sidebarState === 'closed') {
    sidebar.classList.add('close');
}

// Toggle sidebar state and save to localStorage
toggle.addEventListener("click", () => {
    sidebar.classList.toggle("close");
    const isClosed = sidebar.classList.contains("close");
    localStorage.setItem('sidebarState', isClosed ? 'closed' : 'open');
});

// Ensure the sidebar opens when search is clicked
searchBtn.addEventListener("click", () => {
    sidebar.classList.remove("close");
    localStorage.setItem('sidebarState', 'open'); // Save open state to localStorage
});

// Toggle dark mode
modeSwitch.addEventListener("click", () => {
    body.classList.toggle("dark");
    
    if (body.classList.contains("dark")) {
        modeText.innerText = "Light mode";
    } else {
        modeText.innerText = "Dark mode";
    }
});

</script>
@endscript