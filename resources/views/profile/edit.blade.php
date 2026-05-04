<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Mi Lista -->
            <div id="collection" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mi Colección</h3>
                    <p class="text-sm text-gray-600 mb-4">Tu lista de entretenimiento</p>
                    
                    @php
                        $userLists = Auth::user()->userLists()->with('media')->get();
                    @endphp
                    
                    @if($userLists->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($userLists as $item)
                                <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                                    <img src="{{ $item->media->cover_url }}" alt="{{ $item->media->title }}" class="w-full h-32 object-cover">
                                    <div class="p-2">
                                        <h4 class="font-bold text-xs line-clamp-2">{{ $item->media->title }}</h4>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs px-2 py-1 rounded 
                                                @if($item->status === 'completed') bg-green-100 text-green-800
                                                @elseif($item->status === 'watching') bg-blue-100 text-blue-800
                                                @elseif($item->status === 'plan_to_watch') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @if($item->status === 'completed') Completado
                                                @elseif($item->status === 'watching') Viendo
                                                @elseif($item->status === 'plan_to_watch') Plan
                                                @else Descartado @endif
                                            </span>
                                            <a href="{{ route('media.show', $item->media->id) }}" class="text-xs text-blue-600 hover:underline">Ver</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Tu lista está vacía.</p>
                        <a href="/" class="inline-block mt-2 text-blue-600 hover:underline text-sm">Explorar contenido</a>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Listas personalizadas</h3>
                    <p class="text-sm text-gray-600 mb-4">Crea y controla qué listas son públicas o privadas.</p>

                    @php
                        $mediaLists = Auth::user()->mediaLists()->withCount('items')->get();
                    @endphp

                    <form action="{{ route('media-lists.store') }}" method="POST" class="space-y-4 mb-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold mb-2" for="name">Nombre de la lista</label>
                            <input id="name" name="name" type="text" required maxlength="120" class="w-full rounded-lg border-gray-300 bg-gray-50 p-3 text-gray-900" />
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center text-sm text-gray-700">
                                <input type="checkbox" name="is_public" value="1" class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                Hacer pública esta lista
                            </label>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Crear lista
                        </button>
                    </form>

                    @if($mediaLists->isEmpty())
                        <p class="text-sm text-gray-500">Aún no has creado listas personalizadas.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($mediaLists as $list)
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $list->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $list->items_count }} elemento{{ $list->items_count !== 1 ? 's' : '' }}</p>
                                        </div>
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $list->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $list->is_public ? 'Pública' : 'Privada' }}
                                            </span>
                                            <form action="{{ route('media-lists.update', $list) }}" method="POST" class="inline-flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="name" value="{{ $list->name }}">
                                                <input type="hidden" name="is_public" value="{{ $list->is_public ? 0 : 1 }}">
                                                <button type="submit" class="rounded-lg bg-gray-200 px-3 py-1 text-xs text-gray-700 hover:bg-gray-300">
                                                    {{ $list->is_public ? 'Hacer privada' : 'Hacer pública' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('media-lists.destroy', $list) }}" method="POST" class="inline-flex items-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg bg-red-500 px-3 py-1 text-xs text-white hover:bg-red-600">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
