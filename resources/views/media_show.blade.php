<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($media) ? $media->title : (isset($item) ? $item->title : 'Media') }} - {{ config('app.name', 'MyFicList') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-950 text-gray-100">
    @include('layouts.navigation')

    @php
        $m = isset($media) ? $media : (isset($item) ? $item : null);
        $extra = $m ? (is_string($m->extra_data) ? json_decode($m->extra_data, true) : $m->extra_data) : [];
    @endphp

    <header class="bg-gradient-to-r from-gray-900 to-gray-950 border-b border-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-bold text-3xl">{{ $m->title ?? 'Media' }}</h2>
            <a href="/" class="text-blue-400 hover:text-blue-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </header>

    <main class="bg-gray-950 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Cover & Basic Info -->
                    <div class="bg-gray-900 rounded-xl overflow-hidden border border-gray-800 p-6 mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <img src="{{ $m->cover_url }}" alt="{{ $m->title }}" class="w-full rounded-lg shadow-lg">
                            <div class="md:col-span-2">
                                <h1 class="text-4xl font-bold mb-4">{{ $m->title }}</h1>
                                <div class="space-y-4 text-gray-300">
                                    <p><strong>Tipo:</strong> {{ ucfirst($m->media_type) }}</p>
                                    <p><strong>Fuente:</strong> {{ $m->source }}</p>
                                    @if(isset($extra['rating']))
                                        <p><strong>Calificación:</strong> {{ $extra['rating'] }}/10</p>
                                    @endif
                                    @if(isset($extra['year']))
                                        <p><strong>Año:</strong> {{ $extra['year'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Synopsis -->
                    <div class="bg-gray-900 rounded-xl border border-gray-800 p-6 mb-8">
                        <h3 class="text-2xl font-bold mb-4">Sinopsis</h3>
                        <p class="text-gray-300 leading-relaxed">{{ $m->synopsis ?? 'No hay descripción disponible.' }}</p>
                    </div>

                    <!-- Details -->
                    @if($extra)
                        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
                            <h3 class="text-2xl font-bold mb-4">Detalles</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($extra as $key => $value)
                                    @if(is_scalar($value))
                                        <div class="bg-gray-800 p-4 rounded-lg">
                                            <p class="text-gray-500 text-sm capitalize">{{ str_replace('_', ' ', $key) }}</p>
                                            <p class="text-lg font-bold">{{ $value }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Comentarios -->
                <div class="bg-gray-900 rounded-xl border border-gray-800 p-6 mt-8">
                    <h3 class="text-2xl font-bold mb-4">Comentarios</h3>
                    @auth
                    <form action="{{ route('media.comments.store', $m->id) }}" method="POST" class="mb-6">
                        @csrf
                        <textarea name="content" rows="3" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:outline-none mb-2" placeholder="Escribe tu comentario..."></textarea>
                        <input type="hidden" name="media_id" value="{{ $m->id }}">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-lg">Publicar</button>
                    </form>
                    @else
                        <p class="mb-4 text-gray-400">Inicia sesión para comentar.</p>
                    @endauth

                    <!-- Listado de comentarios -->
                    <div id="comments-list">
                        @foreach(App\Models\Comment::where('media_id', $m->id)->whereNull('parent_id')->with('user', 'replies.user')->latest()->get() as $comment)
                            <div class="mb-6 border-b border-gray-800 pb-4">
                                <div class="flex items-center mb-2">
                                    <span class="font-bold text-blue-400 mr-2">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-gray-200 mb-2">{{ $comment->content }}</div>
                                <!-- Respuestas -->
                                @foreach($comment->replies as $reply)
                                    <div class="ml-6 mt-2 border-l-2 border-blue-800 pl-4">
                                        <span class="font-bold text-purple-400 mr-2">{{ $reply->user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                        <div class="text-gray-300">{{ $reply->content }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Add to List Form -->
                    <div class="bg-gradient-to-br from-blue-900/30 to-purple-900/30 border border-blue-800/50 rounded-xl p-6 sticky top-24">
                        <h3 class="text-xl font-bold mb-4">Agregar a Mi Lista</h3>
                        
                        <form action="{{ route('user-list.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="media_id" value="{{ $m->id }}">

                            @auth
                                @php
                                    $mediaLists = Auth::user()->mediaLists()->where('category', $m->media_type)->get();
                                @endphp
                                @if($mediaLists->isNotEmpty())
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Lista de {{ ucfirst($m->media_type) }}</label>
                                        <select name="media_list_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:outline-none">
                                            @foreach($mediaLists as $list)
                                                <option value="{{ $list->id }}">{{ $list->name }} - {{ $list->is_public ? 'Pública' : 'Privada' }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-2 text-xs text-gray-400">Solo se muestran listas dedicadas a este tipo de contenido. Si no hay ninguna, se creará una nueva automáticamente.</p>
                                    </div>
                                @else
                                    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-4 text-sm text-gray-400">
                                        No tienes listas de {{ ucfirst($m->media_type) }} todavía. Se creará una automáticamente cuando guardes este contenido.
                                    </div>
                                @endif
                            @endauth

                            <!-- Status Select -->
                            <div>
                                <label class="block text-sm font-bold mb-2">Estado</label>
                                <select name="status" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:outline-none">
                                    <option value="watching">👀 Viendo</option>
                                    <option value="completed">✅ Completado</option>
                                    <option value="plan_to_watch">📌 Plan a Ver</option>
                                    <option value="dropped">❌ Descartado</option>
                                    <option value="on_hold">⏸️ En espera</option>
                                </select>
                            </div>

                            <!-- Score Input -->
                            <div>
                                <label class="block text-sm font-bold mb-2">Tu Calificación (1-10)</label>
                                <input type="number" name="score" min="1" max="10" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:outline-none">
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 rounded-lg transition">
                                <i class="fas fa-plus mr-2"></i>Agregar a Lista
                            </button>
                        </form>

                        <!-- Quick Stats -->
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <p class="text-xs text-gray-500 mb-2">ID Externo</p>
                            <p class="text-xs text-gray-400 break-all">{{ $m->external_id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>