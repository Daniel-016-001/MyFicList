@extends('layouts.app')

@section('content')
<div class="bg-gray-950 min-h-screen text-gray-100">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-900 rounded-lg p-8 border border-gray-800">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
                <div class="flex items-center gap-6">
                    <img src="{{ $user->avatar_url ?? 'https://via.placeholder.com/100' }}" alt="{{ $user->username }}" class="w-24 h-24 rounded-full">
                    <div>
                        <h1 class="text-3xl font-bold">{{ $user->username }}</h1>
                        <p class="text-gray-400">{{ $user->bio ?? 'Sin bio' }}</p>
                        <p class="text-sm text-gray-500">Completados: {{ $totalCompleted }}</p>
                    </div>
                </div>
                @if($mediaLists->isNotEmpty())
                    <a href="{{ route('media-lists.show', $mediaLists->first()) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-book"></i> Ver lista principal
                    </a>
                @endif
            </div>

            <div class="space-y-10">
                @if($mediaLists->isEmpty())
                    <div class="rounded-3xl border border-gray-800 bg-gray-900 p-10 text-center">
                        <p class="text-gray-400 text-lg">Este usuario no tiene listas públicas todavía.</p>
                    </div>
                @else
                    @foreach($mediaLists as $list)
                        <section class="rounded-3xl border border-gray-800 bg-gray-900 p-6">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h2 class="text-2xl font-semibold">{{ $list->name }}</h2>
                                        <span class="rounded-full px-3 py-1 text-xs uppercase tracking-wide font-semibold {{ $list->is_public ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-200' }}">
                                            {{ $list->is_public ? 'Pública' : 'Privada' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-400">{{ $list->items->count() }} elemento{{ $list->items->count() !== 1 ? 's' : '' }}</p>
                                </div>
                                <a href="{{ route('media-lists.show', $list) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                    <i class="fas fa-eye"></i> Ver lista
                                </a>
                            </div>

                            @if($list->items->isEmpty())
                                <p class="text-gray-400">Esta lista está vacía.</p>
                            @else
                                <div class="grid gap-4 lg:grid-cols-3">
                                    @foreach($list->items as $entry)
                                        <div class="rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                            <img src="{{ $entry->media->cover_url }}" alt="{{ $entry->media->title }}" class="w-full h-40 object-cover">
                                            <div class="p-3 bg-gray-900">
                                                <h3 class="font-semibold text-gray-100 line-clamp-2 mx-2">{{ $entry->media->title }}</h3>
                                                <p class="text-sm text-gray-400">Estado: {{ $entry->status }}</p>
                                                <p class="text-sm text-gray-400">Puntaje: {{ $entry->score ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection