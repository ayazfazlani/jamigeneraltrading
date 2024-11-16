<div>
    <h2>Login</h2>

    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="login"> <!-- Prevent default form submission -->
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" wire:model="email" required>
            @error('email') <span class="error">{{ $message }}</span> @enderror <!-- Display validation error -->
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" wire:model="password" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror <!-- Display validation error -->
        </div>

        <button type="submit">Login</button>
    </form>

    {{-- <div>
        <a href="{{ route('password.request') }}">Forgot your password?</a>
    </div> --}}
</div>