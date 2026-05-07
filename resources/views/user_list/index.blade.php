@extends('layouts.app')

@section('content')
<div class="bg-gray-950 min-h-screen text-gray-100">
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold">Mis Listas</h1>
                <p class="text-gray-400">Administra tus colecciones y controla qué listas puedes compartir.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition">
                <i class="fas fa-home"></i> Volver al inicio
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-green-700 bg-green-900/70 p-4 text-green-100">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-red-700 bg-red-900/70 p-4 text-red-100">
                {{ session('error') }}
            </div>
        @endif

        @if($mediaLists->isEmpty())
            <div class="rounded-3xl border border-gray-800 bg-gray-900 p-10 text-center">
                <p class="text-gray-400 text-lg">Aún no tienes listas creadas.</p>
                <a href="{{ url('/') }}" class="mt-6 inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white py-3 px-5 rounded-lg transition">
                    <i class="fas fa-search"></i> Encuentra contenido para comenzar
                </a>
            </div>
        @else
            @php
                $groupedLists = $mediaLists->groupBy('category');
            @endphp

            @foreach($groupedLists as $category => $lists)
                <section class="mb-10">
                    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-white">{{ ucfirst(str_replace('_', ' ', $category)) }}</h2>
                            <p class="text-sm text-gray-400">Listas dedicadas a {{ ucfirst(str_replace('_', ' ', $category)) }}.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach($lists as $list)
                            <div class="rounded-3xl border border-gray-800 bg-gray-900 p-6">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                                    <div>
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <h3 class="text-2xl font-semibold">{{ $list->name }}</h3>
                                            <span class="rounded-full px-3 py-1 text-xs uppercase tracking-wide font-semibold {{ $list->is_public ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-200' }}">
                                                {{ $list->is_public ? 'Pública' : 'Privada' }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-400">{{ $list->items->count() }} elemento{{ $list->items->count() !== 1 ? 's' : '' }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-3 items-center">
                                        <a href="{{ route('media-lists.show', $list) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                            <i class="fas fa-eye"></i> Ver lista
                                        </a>
                                    </div>
                                </div>

                                @if($list->items->isEmpty())
                                    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6 text-center text-gray-400">
                                        Esta lista está vacía.
                                    </div>
                                @else
                                    <div class="grid gap-4 lg:grid-cols-3">
                                        @foreach($list->items as $entry)
                                            <div class="rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                                <img src="{{ $entry->media->cover_url }}" alt="{{ $entry->media->title }}" class="w-full h-40 object-cover">
                                                <div class="p-3 bg-gray-900">
                                                    <h3 class="text-lg font-semibold text-gray-100 line-clamp-2">{{ $entry->media->title }}</h3>
                                                    <p class="text-sm text-gray-400">Estado: {{ $entry->status }}</p>
                                                    <p class="text-sm text-gray-400">Puntaje: {{ $entry->score ?? 'N/A' }}</p>
                                                    <p class="text-sm text-gray-400">Progreso: {{ $entry->progress }}{{ data_get($entry->media->extra_data, 'episodes') ? ' / ' . data_get($entry->media->extra_data, 'episodes') : '' }}{{ !data_get($entry->media->extra_data, 'episodes') && data_get($entry->media->extra_data, 'chapters') ? ' / ' . data_get($entry->media->extra_data, 'chapters') : '' }}</p>
                                                    @if(!empty(data_get($entry->media->extra_data, 'categories')))
                                                        <p class="text-sm text-gray-400">Categorías: {{ implode(', ', data_get($entry->media->extra_data, 'categories')) }}</p>
                                                    @endif
                                                    <div class="mt-4 flex gap-2">
                                                    <a href="{{ route('media.show', $entry->media->id) }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition">
                                                        Ver
                                                    </a>
                                                    <form action="{{ route('user-list.destroy', $entry->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif
    </main>
</div>
@endsection
