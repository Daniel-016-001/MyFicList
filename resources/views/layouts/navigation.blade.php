<nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50 shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                    <div class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500">
                        MyFicList
                    </div>
                </a>

                <!-- Main Navigation Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-home mr-2"></i>Inicio
                    </a>
                    <a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </a>
                </div>
            </div>

            <!-- Right side: Search + User menu -->
            <div class="flex items-center space-x-4">
                <!-- Quick Search -->
                <form action="{{ url('/search') }}" method="GET" class="hidden md:flex items-center">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Buscar anime, películas..." 
                            class="bg-gray-800 text-white placeholder-gray-500 rounded-lg py-2 pl-10 pr-4 w-48 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-500"></i>
                    </div>
                </form>

                <!-- User Menu -->
                @auth
                    <div class="relative group">
                        <button class="px-3 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white transition flex items-center space-x-2">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                        </button>
                        <div class="absolute right-0 mt-0 w-48 bg-gray-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-gray-700">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-t-lg transition">
                                <i class="fas fa-cog mr-2"></i>Mi Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-b-lg transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
