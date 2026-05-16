<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <!-- Logo & Header -->
        <div class="text-center mb-10 flex flex-col items-center">
            <x-application-logo />
            <p class="text-gray-400 mt-2">Inicia sesión para continuar</p>
        </div>

        <div class="glass rounded-3xl p-8">
            <!-- Session Status -->
            @if (session('status'))
                <div class="p-4 bg-green-500/10 text-green-400 rounded-2xl text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Correo Electrónico
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username" style="color: white !important; background-color: #030712 !important;"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium" placeholder="tu@correo.com">
                    @error('email')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <label for="password" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Contraseña
                    </label>
                    <div class="relative w-full" style="position: relative;">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required
                            autocomplete="current-password"
                            style="color: white !important; background-color: #030712 !important;"
                            class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium pr-12"
                            placeholder="••••••••">
                        <button type="button" @click="show = !show"
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 10;"
                            class="text-gray-500 hover:text-white transition-colors flex items-center justify-center">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me & Forgot password -->
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input name="remember" type="checkbox"
                            class="w-4 h-4 rounded bg-gray-800 border-gray-600 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-400">Recordarme</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-4 bg-blue-600 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-lg transition-all hover:scale-[1.01] mt-10">
                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                </button>

                <!-- Divider -->
                <div class="relative flex items-center gap-4 py-2">
                    <div class="flex-grow h-px bg-gray-700"></div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">o</span>
                    <div class="flex-grow h-px bg-gray-700"></div>
                </div>

                <a href="{{ route('register') }}"
                    class="block w-full py-3 text-center bg-gray-800/50 text-gray-200 hover:text-white font-bold rounded-xl transition-all hover:bg-purple-500/10">
                    Crear una cuenta nueva
                </a>
            </form>
        </div>
    </div>
</x-guest-layout>