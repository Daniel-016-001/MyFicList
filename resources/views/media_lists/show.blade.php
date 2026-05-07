@extends('layouts.app')

@section('content')
<div class="bg-gray-950 min-h-screen text-gray-100">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-900 rounded-lg p-8 border border-gray-800">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold">{{ $mediaList->name }}</h1>
                    <p class="text-gray-400">Lista de {{ $mediaList->user->username }}</p>
                    <p class="text-sm text-gray-500">{{ $mediaList->items->count() }} elemento{{ $mediaList->items->count() !== 1 ? 's' : '' }}</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <span class="rounded-full px-3 py-1 text-xs uppercase tracking-wide font-semibold {{ $mediaList->is_public ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-200' }}">
                        {{ $mediaList->is_public ? 'Pública' : 'Privada' }}
                    </span>
                    <a href="{{ route('users.show', $mediaList->user->username) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-user"></i> Ver perfil
                    </a>
                </div>
            </div>

            @if($mediaList->items->isEmpty())
                <div class="rounded-3xl border border-gray-800 bg-gray-900 p-10 text-center text-gray-400">
                    Esta lista está vacía.
                </div>
            @else
                <div class="grid gap-4 lg:grid-cols-3">
                    @foreach($mediaList->items as $entry)
                        <div class="rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <img src="{{ $entry->media->cover_url }}" alt="{{ $entry->media->title }}" class="w-full h-40 object-cover">
                            <div class="p-3 bg-gray-900">
                                <h2 class="text-lg font-semibold text-gray-100 line-clamp-2">{{ $entry->media->title }}</h2>
                                <p class="text-sm text-gray-400">Estado: {{ $entry->status }}</p>
                                <p class="text-sm text-gray-400">Puntaje: {{ $entry->score ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-400">Progreso: {{ $entry->progress }}{{ data_get($entry->media->extra_data, 'episodes') ? ' / ' . data_get($entry->media->extra_data, 'episodes') : '' }}{{ !data_get($entry->media->extra_data, 'episodes') && data_get($entry->media->extra_data, 'chapters') ? ' / ' . data_get($entry->media->extra_data, 'chapters') : '' }}</p>
                                @if(!empty(data_get($entry->media->extra_data, 'categories')))
                                    <p class="text-sm text-gray-400">Categorías: {{ implode(', ', data_get($entry->media->extra_data, 'categories')) }}</p>
                                @endif
                                <div class="mt-4">
                                    <a href="{{ route('media.show', $entry->media->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition">
                                        Ver contenido
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
