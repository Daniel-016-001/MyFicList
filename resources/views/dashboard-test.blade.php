<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-3xl text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                Mi Dashboard
            </h2>
            <a href="/" class="text-blue-400 hover:text-blue-300 font-bold transition flex items-center space-x-2">
                <i class="fas fa-search"></i><span>Buscar</span>
            </a>
        </div>
    </x-slot>

    <div class="bg-gray-950 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-white">
                <p>Bienvenido, {{ Auth::user()->name }}</p>
                <p class="text-sm text-gray-400">Email: {{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</x-app-layout>