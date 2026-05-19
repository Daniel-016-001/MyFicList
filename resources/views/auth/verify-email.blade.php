@extends('layouts.guest')

@section('title', 'Verifica tu correo')

@section('content')
    <div class="w-full max-w-md mx-auto">
        <!-- Logo & Header -->
        <div class="text-center flex flex-col items-center mb-8">
            <x-application-logo />
            <h2 class="text-2xl font-bold text-white mt-6">Verifica tu correo</h2>
        </div>

        <div class="glass rounded-3xl p-8">
            <div class="mb-6 text-sm text-gray-300 leading-relaxed">
                ¡Gracias por registrarte! Antes de empezar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, te enviaremos otro con gusto.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 font-medium text-sm text-green-400 bg-green-500/10 p-4 rounded-xl border border-green-500/20">
                    Se ha enviado un nuevo enlace de verificación a la dirección de correo que proporcionaste durante el registro.
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-lg transition-all hover:scale-[1.01]">
                        Reenviar correo de verificación
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 text-center bg-gray-800/50 text-gray-300 hover:text-white font-bold rounded-xl transition-all hover:bg-red-500/20 hover:text-red-400">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
