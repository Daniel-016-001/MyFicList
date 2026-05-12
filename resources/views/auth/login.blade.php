<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <!-- Logo & Header -->
        <div class="text-center mb-10">
            <h1 class="text-5xl font-black bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent pb-2">
                MyFicList
            </h1>
            <p class="text-gray-400 mt-2">Inicia sesión para continuar</p>
        </div>

        <div class="glass rounded-3xl p-8">
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-400 rounded-2xl text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Correo Electrónico
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium"
                        placeholder="tu@correo.com">
                    @error('email')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Contraseña
                    </label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium"
                        placeholder="••••••••">
                    @error('password')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me & Forgot password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input name="remember" type="checkbox" class="w-4 h-4 rounded bg-gray-800 border-gray-600 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-400">Recordarme</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-lg transition-all hover:scale-[1.01] mt-2">
                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                </button>

                <!-- Divider -->
                <div class="relative flex items-center gap-4 py-2">
                    <div class="flex-grow h-px bg-gray-700"></div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">o</span>
                    <div class="flex-grow h-px bg-gray-700"></div>
                </div>

                <a href="{{ route('register') }}"
                    class="block w-full py-3 text-center border border-gray-700 hover:border-purple-500/50 text-gray-300 hover:text-white font-bold rounded-xl transition-all hover:bg-purple-500/5">
                    Crear una cuenta nueva
                </a>
            </form>
        </div>
    </div>
</x-guest-layout>
