<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $item->title }}
            </h2>
            <a href="/" class="text-indigo-500 hover:text-indigo-400 font-bold text-sm">← Back to Search</a>
        </div>
    </x-slot>

    <div class="bg-gray-900 min-h-screen">
        @php 
            $extra = $item->extra_data; // Laravel ya lo convierte en array por el 'cast' en el Modelo
            
            // NORMALIZACIÓN DE DATOS (Para que el front no dependa de la API)
            $rating = $extra['rating'] ?? $extra['score'] ?? $extra['metacritic'] ?? 'N/A';
            $trailer = $extra['trailer_url'] ?? null;
            $backdrop = $extra['backdrop'] ?? $item->cover_url;
            $status = $extra['status'] ?? null;
            $release = $extra['release_date'] ?? null;
        @endphp

        <div class="relative w-full h-[500px] overflow-hidden">
            <img src="{{ $backdrop }}" class="absolute inset-0 w-full h-full object-cover opacity-30 blur-sm scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-end pb-10">
                <div class="flex flex-col md:flex-row gap-8 items-center md:items-end w-full">
                    <div class="flex-shrink-0 w-56 md:w-64 shadow-2xl rounded-2xl overflow-hidden border-4 border-gray-800">
                        <img src="{{ $item->cover_url }}" class="w-full h-auto">
                    </div>
                    <div class="flex-grow text-center md:text-left text-white">
                        <div class="flex flex-wrap justify-center md:justify-start gap-3 mb-4">
                            <span class="bg-indigo-600 px-3 py-1 rounded-full text-xs font-black uppercase">
                                {{ $item->media_type }}
                            </span>
                            <span class="bg-yellow-500/20 text-yellow-400 border border-yellow-500/50 px-3 py-1 rounded-full text-xs font-bold">
                                ⭐ {{ $rating }}
                            </span>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black leading-tight">{{ $item->title }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                
                <div class="lg:col-span-2 space-y-12">
                    <section>
                        <h3 class="text-indigo-400 uppercase tracking-widest text-sm font-black mb-4">Synopsis</h3>
                        <p class="text-gray-300 text-xl leading-relaxed">
                            {{ $item->synopsis ?: 'Description not available in English.' }}
                        </p>
                    </section>

                    @if($trailer)
                    <section>
                        <h3 class="text-red-500 uppercase tracking-widest text-sm font-black mb-6">Official Video</h3>
                        <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl bg-black border border-gray-800">
                            <iframe class="w-full h-full" src="{{ $trailer }}" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </section>
                    @endif

                    @if(isset($extra['screenshots']) && is_array($extra['screenshots']))
                    <section>
                        <h3 class="text-green-500 uppercase tracking-widest text-sm font-black mb-6">Screenshots</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach(array_slice($extra['screenshots'], 0, 4) as $screen)
                                <img src="{{ $screen }}" class="rounded-xl border border-gray-700">
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>

                <div class="space-y-8">
                    <div class="bg-gray-800/80 p-8 rounded-3xl border border-gray-700 shadow-2xl">
                        <h4 class="text-white text-xl font-bold mb-6">My List</h4>
                        @auth
                            <form action="{{ route('user-list.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="media_id" value="{{ $item->id }}">
                                <select name="status" class="w-full bg-gray-900 border-gray-700 rounded-xl text-gray-300">
                                    <option value="Watching">Watching</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Plan to Watch">Plan to Watch</option>
                                    <option value="Dropped">Dropped</option>
                                </select>
                                <input type="number" name="score" min="1" max="10" placeholder="Score (1-10)" class="w-full bg-gray-900 border-gray-700 rounded-xl text-gray-300">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl transition">
                                    Save Changes
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block text-center bg-gray-700 text-white py-3 rounded-xl">Login to track</a>
                        @endauth
                    </div>

                    <div class="bg-gray-800/30 p-8 rounded-3xl border border-gray-700/50 text-sm">
                        <h4 class="text-xs font-black text-gray-600 uppercase mb-4 tracking-widest">Technical Info</h4>
                        <div class="space-y-3">
                            @if($release) <p><b class="text-indigo-400">Release:</b> <span class="text-gray-400">{{ $release }}</span></p> @endif
                            @if($status) <p><b class="text-indigo-400">Status:</b> <span class="text-gray-400">{{ $status }}</span></p> @endif
                            
                            @if(isset($extra['episodes'])) <p><b class="text-indigo-400">Episodes:</b> <span class="text-gray-400">{{ $extra['episodes'] }}</span></p> @endif
                            @if(isset($extra['chapters'])) <p><b class="text-indigo-400">Chapters:</b> <span class="text-gray-400">{{ $extra['chapters'] }}</span></p> @endif
                            
                            @if(isset($extra['platforms']))
                                <p><b class="text-indigo-400">Platforms:</b> <span class="text-gray-400">{{ implode(', ', $extra['platforms']) }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>