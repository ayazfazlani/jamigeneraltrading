<div>
<header class="bg-white z-0 shadow-lg px-9 py-5 border-b border-gray-300 flex justify-between items-center">
  <!-- Logo -->
  <div class="flex items-center space-x-3">
      <img src="{{ asset('jgt.png')}}" alt="Logo" class="h-11">
      {{-- <span class="text-xl font-semibold text-gray-700">MyApp</span> --}}
  </div>
  
  <!-- Right Side Icons -->
  <div class="flex items-center space-x-4">
      <!-- Notification Icon -->
       <div class="relative">
         <button id="notificationButton" class="text-gray-500 hover:text-gray-700 focus:outline-none">
              <i class="fas fa-bell text-xl"></i>
          </button>
          <!-- Notification Dropdown -->
          
      </div> 
      
      <!-- User Profile -->
      <div class="relative">
          <button id="userButton" class="flex items-center focus:outline-none space-x-2">
              <img src="https://th.bing.com/th/id/OIP.x7X2oAehk5M9IvGwO_K0PgHaHa?rs=1&pid=ImgDetMain" alt="User Image" class="w-10 h-10 rounded-full border border-gray-300">
              <span class="hidden md:block text-gray-700">{{ Auth::user()->name ?? 'john due'}}</span>
          </button>
          <!-- User Dropdown -->
          <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-10">
              @can('super admin')
              <a wire:navigate href="{{ route('admin')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin</a>
              @endcan
              <a wire:navigate href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
          </div>
      </div>
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