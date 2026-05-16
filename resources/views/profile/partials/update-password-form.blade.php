<section>
    <header>
        <h2 class="text-xl font-bold text-white">
            Seguridad de la cuenta
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            Asegúrate de que tu cuenta use una contraseña larga y aleatoria para mantenerla segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div x-data="{ show: false }">
            <label for="update_password_current_password"
                class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Contraseña actual</label>
            <div class="relative w-full" style="position: relative;">
                <input id="update_password_current_password" name="current_password" :type="show ? 'text' : 'password'"
                    style="color: white !important; background-color: #030712 !important;"
                    class="mt-1 block w-full px-4 py-3 focus:ring-2 focus:ring-blue-600 rounded-xl shadow-sm pr-12"
                    autocomplete="current-password" />
                <button type="button" @click="show = !show"
                    style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 10;"
                    class="text-gray-500 hover:text-white transition-colors flex items-center justify-center">
                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <label for="update_password_password"
                class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Nueva contraseña</label>
            <div class="relative w-full" style="position: relative;">
                <input id="update_password_password" name="password" :type="show ? 'text' : 'password'"
                    style="color: white !important; background-color: #030712 !important;"
                    class="mt-1 block w-full px-4 py-3 focus:ring-2 focus:ring-blue-600 rounded-xl shadow-sm pr-12"
                    autocomplete="new-password" />
                <button type="button" @click="show = !show"
                    style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 10;"
                    class="text-gray-500 hover:text-white transition-colors flex items-center justify-center">
                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <label for="update_password_password_confirmation"
                class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Confirmar nueva
                contraseña</label>
            <div class="relative w-full" style="position: relative;">
                <input id="update_password_password_confirmation" name="password_confirmation"
                    :type="show ? 'text' : 'password'"
                    style="color: white !important; background-color: #030712 !important;"
                    class="mt-1 block w-full px-4 py-3 focus:ring-2 focus:ring-blue-600 rounded-xl shadow-sm pr-12"
                    autocomplete="new-password" />
                <button type="button" @click="show = !show"
                    style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 10;"
                    class="text-gray-500 hover:text-white transition-colors flex items-center justify-center">
                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-500 text-white font-black px-8 py-3 rounded-xl transition shadow-lg shadow-blue-900/20">
                Guardar contraseña
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400 font-bold">¡Contraseña actualizada!</p>
            @endif
        </div>
    </form>
</section>