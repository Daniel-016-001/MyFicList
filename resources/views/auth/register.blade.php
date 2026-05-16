<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <!-- Logo & Header -->
        <div class="text-center flex flex-col items-center">
            <x-application-logo />
            <p class="text-gray-400 mt-2">Crea tu cuenta y empieza tu colección</p>
        </div>

        <div class="glass rounded-3xl p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-8">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Nombre Completo
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        autocomplete="name" style="color: white !important; background-color: #030712 !important;"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium"
                        placeholder="Tu nombre real">
                    @error('name')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Nombre de Usuario (ID único)
                    </label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required
                        autocomplete="username" style="color: white !important; background-color: #030712 !important;"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium" placeholder="ej: daniel99">
                    <p class="mt-1 text-[10px] text-gray-500">Este será tu identificador público en la URL.</p>
                    @error('username')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Correo Electrónico
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
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
                            autocomplete="new-password"
                            style="color: white !important; background-color: #030712 !important;"
                            class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium pr-12"
                            placeholder="Mínimo 8 caracteres">
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

                <!-- Confirm Password -->
                <div x-data="{ show: false }">
                    <label for="password_confirmation"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Confirmar Contraseña
                    </label>
                    <div class="relative w-full" style="position: relative;">
                        <input id="password_confirmation" name="password_confirmation"
                            :type="show ? 'text' : 'password'" required autocomplete="new-password"
                            style="color: white !important; background-color: #030712 !important;"
                            class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium pr-12"
                            placeholder="Repite tu contraseña">
                        <button type="button" @click="show = !show"
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 10;"
                            class="text-gray-500 hover:text-white transition-colors flex items-center justify-center">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-4 bg-blue-600 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-lg transition-all hover:scale-[1.01] mt-10">
                    <i class="fas fa-user-plus mr-2"></i> Crear Cuenta
                </button>

                <!-- Divider -->
                <div class="relative flex items-center gap-4 py-2">
                    <div class="flex-grow h-px bg-gray-700"></div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">o</span>
                    <div class="flex-grow h-px bg-gray-700"></div>
                </div>

                <a href="{{ route('login') }}"
                    class="block w-full py-3 text-center bg-gray-800/50 text-gray-200 hover:text-white font-bold rounded-xl transition-all hover:bg-purple-500/10">
                    Ya tengo cuenta · Iniciar sesión
                </a>
            </form>
        </div>
    </div>
</x-guest-layout>