@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Resultados de búsqueda
            </h1>
            <p class="text-gray-600">
                Buscando: <span class="font-semibold">{{ $query }}</span> 
                ({{ count($results) }} resultados encontrados)
            </p>
        </div>

        @if(empty($results))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-yellow-800">No se encontraron resultados para "{{ $query }}"</p>
            </div>
        @else
            <!-- Grid de resultados -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($results as $result)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <!-- Portada -->
                        <div class="aspect-w-3 aspect-h-4 bg-gray-200 overflow-hidden h-64">
                            @if($result['cover_url'])
                                <img src="{{ $result['cover_url'] }}" 
                                     alt="{{ $result['title'] }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Contenido -->
                        <div class="p-4 flex flex-col h-full">
                            <!-- Título -->
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                {{ $result['title'] }}
                            </h3>

                            <!-- Badge de fuente -->
                            <div class="mb-3">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($result['source'] === 'TMDB') bg-blue-100 text-blue-800
                                    @elseif($result['source'] === 'Jikan') bg-purple-100 text-purple-800
                                    @elseif($result['source'] === 'RAWG') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $result['source'] }}
                                </span>
                            </div>

                            <!-- Sinopsis (preview) -->
                            @if($result['synopsis'])
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3 flex-grow">
                                    {{ substr($result['synopsis'], 0, 150) }}{{ strlen($result['synopsis']) > 150 ? '...' : '' }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500 italic mb-4">Sin descripción disponible</p>
                            @endif

                            <!-- Botones de acción -->
                            <div class="flex gap-2 mt-auto">
                                @if($result['is_stored'])
                                    <!-- Si ya está en BD, mostrar enlace a detalles -->
                                    <a href="{{ route('media.show', $result['id']) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center transition-colors duration-200">
                                        Ver detalles
                                    </a>
                                @else
                                    <!-- Si es un resultado nuevo, mostrar formulario para agregar -->
                                    <form action="{{ route('media.add-from-search') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="external_id" value="{{ $result['external_id'] ?? $result['id'] }}">
                                        <input type="hidden" name="title" value="{{ $result['title'] }}">
                                        <input type="hidden" name="type" value="{{ $type }}">
                                        <input type="hidden" name="source" value="{{ $result['source'] }}">
                                        <input type="hidden" name="cover_url" value="{{ $result['cover_url'] ?? '' }}">
                                        <input type="hidden" name="synopsis" value="{{ $result['synopsis'] ?? '' }}">
                                        <button type="submit" 
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                            + Agregar y ver
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection


@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Resultados de búsqueda
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                {{ count($results) }} resultado(s) encontrado(s) para <strong>"{{ $query }}"</strong>
            </p>
        </div>

        <!-- Results Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($results as $result)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Cover Image -->
                @if($result['cover_url'])
                    <div class="aspect-video bg-gray-200 overflow-hidden">
                        <img src="{{ $result['cover_url'] }}" 
                             alt="{{ $result['title'] }}" 
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-video bg-gray-300 flex items-center justify-center">
                        <span class="text-gray-500">Sin imagen</span>
                    </div>
                @endif

                <!-- Content -->
                <div class="p-4">
                    <!-- Title -->
                    <h2 class="text-lg font-semibold text-gray-900 line-clamp-2">
                        {{ $result['title'] }}
                    </h2>

                    <!-- Source Badge -->
                    <div class="mt-2 flex gap-2 items-center">
                        @if($result['source'] === 'Local')
                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                En tu biblioteca
                            </span>
                        @else
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                {{ $result['source'] }}
                            </span>
                        @endif
                    </div>

                    <!-- Synopsis -->
                    <p class="mt-3 text-sm text-gray-600 line-clamp-3">
                        {{ $result['synopsis'] }}
                    </p>

                    <!-- Action Button -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        @if($result['is_stored'])
                            <!-- Already in database - View button -->
                            <a href="{{ route('media.show', $result['id']) }}" 
                               class="w-full block text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition">
                                Ver detalles
                            </a>
                        @else
                            <!-- Not in database - Add button -->
                            <form action="{{ route('media.add-from-search') }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="type" value="{{ $type }}">
                                <input type="hidden" name="source" value="{{ $result['source'] }}">
                                <input type="hidden" name="external_id" value="{{ $result['external_id'] }}">
                                <input type="hidden" name="title" value="{{ $result['title'] }}">
                                
                                <button type="submit" 
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded transition">
                                    + Agregar y ver
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- No Results -->
        @if(empty($results))
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">
                No se encontraron resultados para "<strong>{{ $query }}</strong>"
            </p>
            <a href="{{ route('dashboard') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded">
                Volver al inicio
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
