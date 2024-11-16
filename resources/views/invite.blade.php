<form action="{{ route('invite.send') }}" method="POST">
  @csrf
  <label for="email">Email:</label>
  <input type="email" name="email" required>
  <button type="submit">Send Invitation</button>
</form>

@if(session('status'))
  <p>{{ session('status') }}</p>
@endif
