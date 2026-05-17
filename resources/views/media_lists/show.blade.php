@extends('layouts.app')
@section('title', $mediaList->name)

@section('content')
<div class="bg-gray-950 min-h-screen text-gray-100">
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
                    @if($mediaList->user->username)
                        <a href="{{ route('users.show', $mediaList->user->username) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-user"></i> Ver perfil
                        </a>
                    @endif
                </div>
            </div>

            @if($mediaList->items->isEmpty())
                <div class="rounded-3xl border border-gray-800 bg-gray-900 p-10 text-center text-gray-400">
                    Esta lista está vacía.
                </div>
            @else
                <div class="columns-2 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 gap-6">
                    @foreach($mediaList->items as $entry)
                        @php
                            $media = $entry->media;
                        @endphp
                        <div class="break-inside-avoid mb-6">
                            <div class="bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.02] border border-blue-900/20 flex flex-col">
                                @if($media)
                                    <a href="{{ route('media.show', $media->id) }}" class="block group cursor-pointer">
                                @else
                                    <div class="block group opacity-50">
                                @endif
                                    <div class="relative">
                                        <img src="{{ $media ? $media->cover_url : '' }}" alt="{{ $media ? $media->title : 'N/A' }}" class="w-full h-auto object-cover brightness-90 group-hover:brightness-100 transition-all">
                                        @if($entry->score)
                                            <div style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.75); border-radius: 0.5rem; padding: 0.25rem 0.5rem; z-index: 10; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;" class="backdrop-blur-sm shadow-lg text-yellow-400 text-xs font-black">
                                                <i class="fas fa-star text-[9px]"></i> {{ $entry->score }}
                                            </div>
                                        @endif
                                    </div>
                                @if($media) </a> @else </div> @endif
                                
                                <div class="p-5 flex-grow flex flex-col">
                                    <h3 class="font-bold text-lg text-white leading-tight mb-4">{{ $media ? $media->title : 'Elemento eliminado' }}</h3>
                                    
                                    <div class="flex flex-col gap-1.5 mb-4 text-[11px] font-bold uppercase tracking-wider">
                                        <div class="text-gray-500">Estado: <span class="text-blue-400">{{ ucfirst(str_replace('_', ' ', $entry->status)) }}</span></div>

                                        @if($media->media_type !== 'game')
                                            <!-- Progreso eliminado por solicitud del usuario -->
                                        @endif
                                    </div>

                                    <div class="mt-auto flex items-center justify-between pt-4 border-t border-slate-800/50">
                                        @if($media)
                                            <a href="{{ route('media.show', $media->id) }}" class="text-gray-400 hover:text-white flex items-center gap-1.5 text-xs font-bold transition-colors ml-auto">
                                                Detalles <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Comments Section -->
        <div class="mt-8 bg-gray-900 rounded-lg p-8 border border-gray-800">
            <x-comments :model="$mediaList" />
        </div>
    </div>
</div>
@endsection
