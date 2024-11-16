<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Stock Management' }}</title>

    <!-- Link to external styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <link href="/dist/tailwind.css" rel="stylesheet" />

    {{-- fav icon  --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('Icon.png')}}">
    @livewireStyles
</head>
<body>

    <!-- Sidebar Component -->
    @livewire('header')

    <div class="flex">
    @livewire('sidebar')


    <!-- Main Content Section -->
    <div class="flex-1 overflow-x-hidden">
        {{ $slot }}  <!-- This will display the content of Livewire components -->
    </div>
</div>
    <!-- Scripts Section -->
    @livewireScripts
    @yield('scripts')
    @stack('scripts')
    <!-- Alpine.js for Dropdown functionality -->
    <script src="//unpkg.com/alpinejs"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>

</body>
</html>
