@extends('layouts.app')

@section('content')
<div class="bg-gray-950 min-h-screen py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-12">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl md:text-5xl font-black mb-2 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                        Resultados de búsqueda
                    </h1>
                    <p class="text-gray-400 text-lg">
                        <span class="text-yellow-400 font-semibold">{{ count($results) }}</span> resultado{{ count($results) !== 1 ? 's' : '' }} encontrado{{ count($results) !== 1 ? 's' : '' }}
                        @if($query)
                            para <span class="text-blue-400 font-semibold">"{{ $query }}"</span>
                        @endif
                        en <span class="text-purple-400 font-semibold">{{ ucfirst($type) }}</span>
                    </p>
                </div>
                <a href="/" class="flex items-center space-x-2 bg-gray-900 hover:bg-gray-800 border border-gray-800 text-gray-300 hover:text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-plus"></i><span>Nueva Búsqueda</span>
                </a>
            </div>
        </div>

        <!-- Empty State -->
        @if(empty($results))
            <div class="text-center py-20">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold text-gray-300 mb-2">No se encontraron resultados</h2>
                <p class="text-gray-500 mb-8">
                    No pudimos encontrar resultados para "<span class="font-semibold">{{ $query }}</span>"
                </p>
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <a href="/" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold px-6 py-3 rounded-lg transition">
                        <i class="fas fa-search mr-2"></i>Intentar otra búsqueda
                    </a>
                    <a href="/" class="inline-block bg-gray-900 hover:bg-gray-800 border border-gray-800 text-white font-bold px-6 py-3 rounded-lg transition">
                        <i class="fas fa-home mr-2"></i>Ir al inicio
                    </a>
                </div>
            </div>
        @else
            <!-- Results Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($results as $result)
                    <div class="group relative bg-gray-900 border border-gray-800 rounded-lg overflow-hidden hover:border-blue-500 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-105">
                        
                        <!-- Cover Image Container -->
                        <div class="relative overflow-hidden h-80 bg-gray-800">
                            @if($result['cover_url'])
                                <img src="{{ $result['cover_url'] }}" 
                                     alt="{{ $result['title'] }}" 
                                     class="w-full h-full object-cover group-hover:brightness-50 transition duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-600 text-4xl"></i>
                                </div>
                            @endif

                            <!-- Overlay Info - Only on hover -->
                            <div class="absolute inset-0 bg-black/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-4">
                                <div class="text-right">
                                    <span class="inline-block bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded text-xs font-bold text-gray-200">
                                        {{ ucfirst($result['media_type'] ?? $type) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-white text-sm leading-relaxed line-clamp-3">
                                        {{ substr($result['synopsis'] ?? 'Sin descripción', 0, 100) }}...
                                    </p>
                                </div>
                            </div>

                            <!-- Badge's -->
                            <div class="absolute top-2 left-2 flex gap-2">
                                <span class="inline-block px-2 py-1 text-xs font-bold rounded backdrop-blur-sm
                                    @if($result['source'] === 'TMDB') bg-blue-600/80 text-blue-100
                                    @elseif($result['source'] === 'Jikan') bg-purple-600/80 text-purple-100
                                    @elseif($result['source'] === 'RAWG') bg-green-600/80 text-green-100
                                    @else bg-gray-700/80 text-gray-100 @endif">
                                    {{ $result['source'] }}
                                </span>
                                @if($result['is_stored'])
                                    <span class="inline-block px-2 py-1 text-xs font-bold bg-green-600/80 text-green-100 rounded backdrop-blur-sm">
                                        ✓ Guardado
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4 flex flex-col h-32">
                            <!-- Title -->
                            <h3 class="text-sm font-bold text-white line-clamp-2 mb-2 group-hover:text-blue-400 transition">
                                {{ $result['title'] }}
                            </h3>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 mt-auto">
                                @if($result['is_stored'])
                                    <!-- If already in DB, show details link -->
                                    <a href="{{ route('media.show', $result['id']) }}" 
                                       class="flex-1 text-center bg-blue-600/80 hover:bg-blue-600 text-white text-xs font-bold py-2 rounded transition">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <!-- If new result, show add button -->
                                    <form action="{{ route('media.add-from-search') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="external_id" value="{{ $result['external_id'] ?? $result['id'] }}">
                                        <input type="hidden" name="title" value="{{ $result['title'] }}">
                                        <input type="hidden" name="type" value="{{ $type }}">
                                        <input type="hidden" name="source" value="{{ $result['source'] }}">
                                        <input type="hidden" name="cover_url" value="{{ $result['cover_url'] ?? '' }}">
                                        <input type="hidden" name="synopsis" value="{{ $result['synopsis'] ?? '' }}">
                                        <button type="submit" 
                                                class="w-full bg-green-600/80 hover:bg-green-600 text-white text-xs font-bold py-2 rounded transition">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- View Details -->
                                <a href="{{ $result['is_stored'] ? route('media.show', $result['id']) : '#' }}" 
                                   class="flex-1 text-center bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white text-xs font-bold py-2 rounded transition">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Info -->
            <div class="mt-12 text-center text-gray-500">
                <p>Mostrando {{ count($results) }} de {{ count($results) }} resultados</p>
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
@include('layouts.footer')
