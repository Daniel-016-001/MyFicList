<section class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-red-500">
            Eliminar Cuenta
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            Una vez que tu cuenta sea eliminada, todos sus recursos y datos se borrarán permanentemente. Antes de eliminar tu cuenta, por favor descarga cualquier dato o información que desees conservar.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-500 text-white font-black px-8 py-3 rounded-xl transition shadow-lg shadow-red-900/20">
        Eliminar cuenta
    </button></section>