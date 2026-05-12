<header>
    <h2 class="text-xl font-bold text-white">
        Información del Perfil
    </h2>
    <p class="mt-1 text-sm text-gray-400">
        Actualiza tu foto de perfil, biografía e información de contacto.
    </p>
</header>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<!-- Formulario independiente para el AVATAR -->
<form method="post" action="{{ route('profile.update') }}" class="mt-6" enctype="multipart/form-data">
    @csrf
    @method('patch')
    
    <!-- Estos campos ocultos son necesarios para que la validación general no falle al subir solo la foto -->
    <input type="hidden" name="name" value="{{ $user->name }}">
    <input type="hidden" name="email" value="{{ $user->email }}">
    <input type="hidden" name="username" value="{{ $user->username }}">

    <div class="relative py-4">
        <div class="flex flex-col md:flex-row items-center gap-10">
            <!-- Avatar Display -->
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" 
                    class="relative w-32 h-32 rounded-full object-cover border-2 border-white/10 shadow-2xl">
                <label for="avatar" class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition-all cursor-pointer backdrop-blur-sm">
                    <i class="fas fa-camera text-white text-xl"></i>
                </label>
            </div>

            <!-- Upload Info & Button -->
            <div class="flex-1 text-center md:text-left">
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-white mb-1 tracking-tight">Tu Foto de Perfil</h4>
                    <p class="text-[11px] text-gray-500 font-medium">Formato cuadrado recomendado.</p>
                </div>
                
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                    <div class="relative">
                        <input id="avatar" name="avatar" type="file" class="hidden" onchange="document.getElementById('file-name').textContent = this.files[0].name" />
                        <label for="avatar" class="cursor-pointer px-6 py-3 bg-gray-800 hover:bg-gray-700 border border-white/10 rounded-xl text-xs font-bold text-white transition-all inline-flex items-center shadow-lg">
                            <i class="fas fa-image mr-2 text-blue-400"></i> Elegir Imagen
                        </label>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-8 py-3 rounded-2xl transition-all text-sm shadow-lg shadow-blue-900/20 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Actualizar Foto
                    </button>
                </div>
                <div id="file-name" class="text-xs text-blue-400 font-medium italic mt-2 min-h-[1rem]"></div>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>
    </div>
</form>

<!-- Formulario independiente para la INFORMACIÓN -->
<form method="post" action="{{ route('profile.update') }}" class="mt-12 space-y-6">
    @csrf
    @method('patch')

    <div class="space-y-6">
        <div>
            <label for="name" class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-2">Nombre Completo</label>
            <input id="name" name="name" type="text" style="color: white !important; background-color: #030712 !important;" class="mt-1 block w-full border border-white/10 focus:ring-2 focus:ring-blue-600 rounded-xl shadow-sm text-sm" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="username" class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-2">Nombre de Usuario</label>
            <input id="username" name="username" type="text" style="color: white !important; background-color: #030712 !important;" class="mt-1 block w-full border border-white/10 focus:ring-2 focus:ring-blue-600 rounded-xl shadow-sm text-sm" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <label for="bio" class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-2">Biografía</label>
            <textarea id="bio" name="bio" style="color: white !important; background-color: #030712 !important;" class="mt-1 block w-full border border-white/10 focus:ring-2 focus:ring-blue-600 rounded-xl shadow-sm text-sm" rows="3" placeholder="Cuéntanos algo sobre ti...">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <label for="email" class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-2">Email</label>
            <input id="email" name="email" type="email" style="color: white !important; background-color: #030712 !important;" class="mt-1 block w-full border border-white/10 focus:ring-2 focus:ring-blue-600 rounded-xl shadow-sm text-sm" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>
    </div>

    <div class="flex items-center gap-4 pt-4">
        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-8 py-3 rounded-2xl transition-all text-sm shadow-lg shadow-blue-900/20">
            Guardar Cambios
        </button>

        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-green-400 font-bold"
            >¡Actualizado!</p>
        @endif
    </div>
</form>
