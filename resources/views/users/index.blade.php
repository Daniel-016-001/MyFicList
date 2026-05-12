@extends('layouts.app')

@section('content')
<div class="bg-gray-950 min-h-screen text-gray-100">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold mb-8">Comunidad</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($users as $user)
                <div class="bg-gray-900 rounded-lg p-6 border border-gray-800">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $user->avatar_url ?? 'https://via.placeholder.com/50' }}" alt="{{ $user->username }}" class="w-12 h-12 rounded-full">
                        <div>
                            <h3 class="text-lg font-semibold">
                                <a href="{{ route('users.show', $user) }}" class="text-blue-400 hover:text-blue-300">{{ $user->username ?: $user->name }}</a>
                            </h3>
                            <p class="text-gray-400">{{ $user->bio ?? 'Sin bio' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $users->links() }}
    </div>
</div>
@endsection