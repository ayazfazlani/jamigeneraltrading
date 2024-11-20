<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        @if (session()->has('error'))
            <div class="mb-4 text-red-500">{{ session('error') }}</div>
        @endif
        <form wire:submit.prevent="login">
            <div class="mb-4">
                <input type="email" wire:model="email" placeholder="Email" class="border rounded w-full p-2" required>
            </div>
            <div class="mb-4">
                <input type="password" wire:model="password" placeholder="Password" class="border rounded w-full p-2" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white rounded w-full py-2">Login</button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                Forgot your password?
            </a>
        </div>
    </div>
</div>