
<div class="relative">
    <select
        wire:model="currentTeamId"
        wire:change="switchTeam"
        class="block w-40 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    >
        @foreach($teams as $team)
            <option value="{{ $team->id }}" {{ $team->id == $currentTeamId ? 'selected' : '' }}>
                {{ $team->name }}
            </option>
        @endforeach
    </select>

    @if (session()->has('status'))
        <div class="absolute z-50 top-full right-0 mt-1 w-48 bg-green-100 border border-green-400 text-green-700 px-3 py-1 rounded text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="absolute z-50 top-full right-0 mt-1 w-48 bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded text-sm">
            {{ session('error') }}
        </div>
    @endif
</div>