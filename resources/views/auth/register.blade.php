<form action="{{ route('register') }}" method="POST">
  @csrf
  <input type="hidden" name="token" value="{{ request()->query('token') }}">

  <label for="name">Name:</label>
  <input type="text" name="name" required>

  <label for="email">Email:</label>
  {{-- <input type="email" name="email" value="{{ request()->query('email') }}" required readonly> --}}
  <input type="email" name="email" value="" required>

  <label for="password">Password:</label>
  <input type="password" name="password" required>

  <label for="password_confirmation">Confirm Password:</label>
  <input type="password" name="password_confirmation" required>

  <button type="submit">Register</button>
</form>

@if(session('error'))
  <p>{{ session('error') }}</p>
  @elseif(session('status'))
  <p>{{ session('status') }}</p>
@endif
