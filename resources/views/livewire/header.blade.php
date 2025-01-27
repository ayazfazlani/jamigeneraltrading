<div>
    <header class="bg-white  z-0 shadow-lg px-9 py-5 border-b border-gray-300 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex h-10 w-40 items-center space-x-3">
            <img src="{{ asset('jgt.png') }}" alt="Logo" class="h-11">
        </div>

        <!-- Right Side Icons -->
        <div class="flex items-center space-x-4">
            @auth
                <!-- Notification Icon -->
                <div class="relative">
                    <button id="notificationButton" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fas fa-bell text-xl"></i>
                    </button>
                    <!-- Notification Dropdown -->
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-10">
                        <!-- Add notification items here -->
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Notification 1</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Notification 2</a>
                    </div>
                </div>

                <!-- Team Switcher -->
                @if(auth()->user()->teams->count() > 1)
                    <div class="relative">
                        @livewire('team-switcher') <!-- Assuming you have a Livewire component for team switching -->
                    </div>
                @endif

                <!-- User Profile -->
                <div class="relative">
                    <button id="userButton" class="flex items-center focus:outline-none space-x-2">
                        <img src="https://th.bing.com/th/id/OIP.x7X2oAehk5M9IvGwO_K0PgHaHa?rs=1&pid=ImgDetMain" alt="User Image" class="w-10 h-10 rounded-full border border-gray-300">
                        <span class="hidden md:block text-gray-700">{{ Auth::user()->name ?? 'John Doe' }}</span>
                    </button>
                    <!-- User Dropdown -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-10">
                        <a wire:navigate href="{{ route('admin') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin</a>
                        <a wire:navigate href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            @endauth
        </div>
    </header>
</div>

@script
<script>
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');

    notificationButton.addEventListener('click', () => {
        notificationDropdown.classList.toggle('hidden');
    });

    // Toggle User Dropdown
    const userButton = document.getElementById('userButton');
    const userDropdown = document.getElementById('userDropdown');

    userButton.addEventListener('click', () => {
        userDropdown.classList.toggle('hidden');
    });

    // Close dropdowns if clicking outside
    window.addEventListener('click', (e) => {
        if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
        if (!userButton.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.add('hidden');
        }
    });
</script>
@endscript