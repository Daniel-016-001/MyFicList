@extends('layouts.app')

@section('content')
    <div class="bg-gray-950 min-h-screen py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1
                            class="text-4xl md:text-5xl font-black mb-2 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                            Resultados de búsqueda
                        </h1>
                        <p class="text-gray-400 text-lg">
                            <span class="text-yellow-400 font-semibold">{{ count($results) }}</span>
                            resultado{{ count($results) !== 1 ? 's' : '' }} encontrado{{ count($results) !== 1 ? 's' : '' }}
                            @if(isset($query))
                                para <span class="text-blue-400 font-semibold">"{{ $query }}"</span>
                            @endif
                        </p>
                    </div>
                    <a href="/"
                        class="flex items-center space-x-2 bg-gray-900/50 hover:bg-gray-800 border border-gray-800 text-gray-300 hover:text-white px-6 py-3 rounded-xl transition-all shadow-lg backdrop-blur-md">
                        <i class="fas fa-search"></i><span>Nueva búsqueda</span>
                    </a>
                </div>
            </div>

            @if(empty($results))
                <div class="text-center py-20 bg-gray-900/30 rounded-3xl border border-gray-800 backdrop-blur-sm">
                    <div class="text-7xl mb-6">🔍</div>
                    <h2 class="text-2xl font-bold text-gray-300 mb-2">No se encontraron resultados</h2>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">
                        No pudimos encontrar nada para tu búsqueda. Prueba con otros términos o revisa la ortografía.
                    </p>
                    <a href="/"
                        class="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold px-8 py-4 rounded-xl transition-all shadow-xl">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a intentarlo
                    </a>
                </div>
            @else
                <!-- Results Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                    @foreach($results as $result)
                        <div
                            class="group relative bg-gray-900/50 backdrop-blur-sm border border-gray-800 rounded-2xl overflow-hidden hover:border-blue-500/50 transition-all duration-500 shadow-xl hover:shadow-blue-900/20 transform hover:-translate-y-2">

                            <!-- Image Container -->
                            <div class="relative aspect-[2/3] overflow-hidden">
                                @if($result['cover_url'])
                                    <img src="{{ $result['cover_url'] }}" alt="{{ $result['title'] }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 brightness-90 group-hover:brightness-110"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x600/1f2937/9ca3af?text=Sin+Imagen';">
                                @else
                                    <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-700 text-5xl"></i>
                                    </div>
                                @endif

                                <!-- Source Badge -->
                                <div class="absolute top-3 left-3 flex flex-col gap-2">
                                    <span class="px-2 py-1 text-[10px] font-black uppercase tracking-wider rounded-md backdrop-blur-md shadow-lg
                                                                                    @if($result['source'] === 'TMDB') bg-blue-600/90 text-white
                                                                                    @elseif($result['source'] === 'Jikan') bg-purple-600/90 text-white
                                                                                    @elseif($result['source'] === 'RAWG') bg-green-600/90 text-white
                                                                                    @else bg-gray-700/90 text-white @endif">
                                        {{ $result['source'] }}
                                    </span>
                                    @if(isset($result['is_stored']) && $result['is_stored'])
                                        <span
                                            class="px-2 py-1 text-[10px] font-black uppercase tracking-wider bg-yellow-500/90 text-gray-950 rounded-md backdrop-blur-md shadow-lg">
                                            <i class="fas fa-check mr-1"></i>En lista
                                        </span>
                                    @endif
                                </div>

                                <!-- Overlay on Hover -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-6">
                                    <p
                                        class="text-gray-300 text-xs leading-relaxed line-clamp-4 mb-4 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                        {{ $result['synopsis'] ?? 'Sin descripción disponible.' }}
                                    </p>
                                    <a href="{{ route('media.details', ['external_id' => $result['external_id'] ?? $result['id'], 'source' => $result['source'], 'type' => $result['media_type'] ?? ($type ?? 'anime')]) }}"
                                        class="w-full py-3 bg-white text-gray-950 text-center rounded-xl font-bold text-sm hover:bg-blue-400 transition-colors duration-300">
                                        Ver detalles
                                    </a>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-5">
                                <h3
                                    class="text-white font-bold text-sm line-clamp-2 min-h-[40px] group-hover:text-blue-400 transition-colors">
                                    {{ $result['title'] }}
                                </h3>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                        {{ ucfirst($result['media_type'] ?? ($type ?? 'Media')) }}
                                    </span>
                                    @if(isset($result['year']))
                                        <span class="text-[10px] text-gray-400 font-bold bg-gray-800 px-2 py-0.5 rounded">
                                            {{ $result['year'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Info -->
                <div class="mt-16 text-center">
                    <div class="inline-block p-1 rounded-full bg-gray-900/50 border border-gray-800 backdrop-blur-sm">
                        <p class="px-6 py-2 text-gray-500 text-sm font-medium">
                            Mostrando todos los resultados encontrados
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection