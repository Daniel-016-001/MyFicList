@extends('layouts.guest')

@section('title', 'Recuperar contraseña')

@section('content')
    <div class="w-full max-w-md mx-auto">
        <!-- Logo & Header -->
        <div class="text-center flex flex-col items-center mb-8">
            <x-application-logo />
            <h2 class="text-2xl font-bold text-white mt-6">Recuperar contraseña</h2>
        </div>

        <div class="glass rounded-3xl p-8">
            <div class="mb-6 text-sm text-gray-300 leading-relaxed">
                ¿Olvidaste tu contraseña? No hay problema. Simplemente indícanos tu dirección de correo electrónico y te enviaremos un enlace para que puedas elegir una nueva.
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 font-medium text-sm text-green-400 bg-green-500/10 p-4 rounded-xl border border-green-500/20">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-4 text-gray-500"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-gray-900/50 border border-gray-700/50 text-white rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder-gray-600"
                            placeholder="tu@correo.com">
                    </div>
                    @error('email')
                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-lg transition-all hover:scale-[1.01]">
                        Enviar enlace de recuperación
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
