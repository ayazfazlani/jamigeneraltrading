<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Invite User</h2>
        <form wire:submit.prevent="sendInvitation">
            <div class="mb-4">
                <input type="email" wire:model="email" placeholder="User Email" class="border rounded w-full p-2" required>
            </div>
            <button type="submit" class="bg-green-500 text-white rounded w-full py-2">Send Invitation</button>
        </form>
        @if (session()->has('message'))
            <p class="mt-4 text-green-500">{{ session('message') }}</p>
        @elseif (session()->has('error'))
            <p class="mt-4 text-red-500">{{ session('error') }}</p>
        @endif
    </div>
</div>