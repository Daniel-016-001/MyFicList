<nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50 shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-8">
                <a href="/" class="flex items-center space-x-2 no-underline" style="min-width: max-content;">
                    <x-application-logo />
                </a>

                <!-- Main Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="/"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-home mr-2"></i>Inicio
                    </a>
                    <a href="{{ route('media.explore') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('media.explore') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-search mr-2"></i>Explorar
                    </a>
                    <a href="{{ route('dashboard') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-award mr-2"></i>Fiction top
                    </a>
                    <a href="{{ route('forum.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('forum.index') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-comments mr-2"></i>Foro
                    </a>
                </div>
            </div>

            <!-- Right side: Search + User menu -->
            <div class="flex items-center space-x-4">
                <!-- Quick Search -->
                <form action="{{ url('/search/unified') }}" method="GET" class="hidden md:flex items-center">
                    <input type="hidden" name="type" value="all">
                    <div class="relative">
                        <input type="text" name="query" placeholder="Buscar en todas las categorías..."
                            class="bg-gray-800 text-white placeholder-gray-500 rounded-lg py-2 pl-10 pr-4 w-48 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-500"></i>
                    </div>
                </form>

                <!-- User Menu -->
                @auth
                    <div class="relative group">
                        <button
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white transition flex items-center space-x-2">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->username }}"
                                class="w-8 h-8 rounded-full object-cover border border-gray-700">
                            <span class="hidden md:inline">{{ Auth::user()->username }}</span>
                        </button>
                        <div
                            class="absolute right-0 mt-0 w-48 bg-gray-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-gray-700">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-t-lg transition">
                                <i class="fas fa-user mr-2"></i>Mi perfil
                            </a>
                            <a href="{{ route('user-list.index') }}"
                                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <i class="fas fa-list mr-2"></i>Mi lista
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-b-lg transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-2">
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>