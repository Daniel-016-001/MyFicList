@extends('layouts.guest')

@section('title', 'Confirmar contraseña')

@section('content')
    <div class="w-full max-w-md mx-auto">
        <!-- Logo & Header -->
        <div class="text-center flex flex-col items-center mb-8">
            <x-application-logo />
            <h2 class="text-2xl font-bold text-white mt-6">Seguridad</h2>
        </div>

        <div class="glass rounded-3xl p-8">
            <div class="mb-6 text-sm text-gray-300 leading-relaxed text-center">
                Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-4 text-gray-500"></i>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full bg-gray-900/50 border border-gray-700/50 text-white rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder-gray-600">
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-lg transition-all hover:scale-[1.01]">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
