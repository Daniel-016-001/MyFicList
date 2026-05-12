<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <!-- Logo & Header -->
        <div class="text-center mb-10">
            <h1 class="text-5xl font-black bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent pb-2">
                MyFicList
            </h1>
            <p class="text-gray-400 mt-2">Crea tu cuenta y empieza tu colección</p>
        </div>

        <div class="glass rounded-3xl p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Nombre de usuario
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium"
                        placeholder="Tu nombre o alias">
                    @error('name')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Correo Electrónico
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
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
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium"
                        placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                        Confirmar Contraseña
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        class="input-field w-full rounded-xl px-4 py-3 text-sm font-medium"
                        placeholder="Repite tu contraseña">
                    @error('password_confirmation')
                        <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-4 bg-blue-600 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-black rounded-xl shadow-lg transition-all hover:scale-[1.01] mt-2">
                    <i class="fas fa-user-plus mr-2"></i> Crear Cuenta
                </button>

                <!-- Divider -->
                <div class="relative flex items-center gap-4 py-2">
                    <div class="flex-grow h-px bg-gray-700"></div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">o</span>
                    <div class="flex-grow h-px bg-gray-700"></div>
                </div>

                <a href="{{ route('login') }}"
                    class="block w-full py-3 text-center bg-gray-800/50 border border-gray-700 hover:border-purple-500/50 text-gray-200 hover:text-white font-bold rounded-xl transition-all hover:bg-purple-500/10">
                    Ya tengo cuenta · Iniciar sesión
                </a>
            </form>
        </div>
    </div>
</x-guest-layout>
