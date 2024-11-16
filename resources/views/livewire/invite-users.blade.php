<div>
    <form wire:submit.prevent="sendInvitation">
        <input type="email" wire:model="email" placeholder="User Email" required>
        <button type="submit">Send Invitation</button>
    </form>
    @if (session()->has('message'))
        <p>{{ session('message') }}</p>
    @elseif (session()->has('error'))
        <p>{{ session('error') }}</p>
    @endif
</div>
