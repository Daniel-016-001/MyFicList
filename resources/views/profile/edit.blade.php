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
