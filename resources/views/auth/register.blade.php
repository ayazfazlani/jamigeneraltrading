<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Name Field -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" 
                    class="border rounded w-full p-2 mt-1 focus:ring-blue-500 focus:border-blue-500"
                    required autofocus>
                @error('name')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Field -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" 
                    class="border rounded w-full p-2 mt-1 focus:ring-blue-500 focus:border-blue-500"
                    required>
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" 
                    class="border rounded w-full p-2 mt-1 focus:ring-blue-500 focus:border-blue-500"
                    required>
                @error('password')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" 
                    class="border rounded w-full p-2 mt-1 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="bg-blue-500 text-white rounded w-full py-2 hover:bg-blue-600">
                Register
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500">
                Already have an account? Login
            </a>
        </div>
    </div>
</div>
