<nav x-data="{ mobileMenuOpen: false }" class="bg-gray-900 sticky top-0 z-50 shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-4 xl:space-x-8">
                <a href="/" class="flex items-center space-x-2 no-underline" style="min-width: max-content;">
                    <x-application-logo />
                </a>

                <!-- Main Navigation Links (Desktop) -->
                <div class="hidden lg:flex items-center space-x-0.5 xl:space-x-2">
                    <a href="/"
                        class="px-2.5 py-2 xl:px-3 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-home mr-1.5 xl:mr-2"></i>Inicio
                    </a>
                    <a href="{{ route('media.explore') }}"
                        class="px-2.5 py-2 xl:px-3 rounded-md text-sm font-medium {{ request()->routeIs('media.explore') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-search mr-1.5 xl:mr-2"></i>Explorar
                    </a>
                    <a href="{{ route('dashboard') }}"
                        class="px-2.5 py-2 xl:px-3 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-award mr-1.5 xl:mr-2"></i>Fiction top
                    </a>
                    <a href="{{ route('forum.index') }}"
                        class="px-2.5 py-2 xl:px-3 rounded-md text-sm font-medium {{ request()->routeIs('forum.index') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} transition">
                        <i class="fas fa-comments mr-1.5 xl:mr-2"></i>Foro
                    </a>
                </div>
            </div>

            <!-- Right side: Search + User menu -->
            <div class="flex items-center space-x-2.5 xl:space-x-4">
                <!-- Quick Search -->
                <form action="{{ url('/search/unified') }}" method="GET" class="hidden lg:flex items-center">
                    <input type="hidden" name="type" value="all">
                    <div class="relative">
                        <input type="text" name="query" placeholder="Buscar..."
                            class="bg-gray-800 text-white placeholder-gray-500 rounded-lg py-2 pl-10 pr-4 w-32 lg:w-36 xl:w-48 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-500"></i>
                    </div>
                </form>

                <!-- User Menu (Desktop) -->
                @auth
                    <div class="relative group hidden lg:block">
                        <button
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white transition flex items-center space-x-2">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->username }}"
                                class="w-8 h-8 rounded-full object-cover">
                            <span>{{ Auth::user()->username }}</span>
                        </button>
                        <div
                            class="absolute right-0 mt-0 w-48 bg-gray-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <a href="{{ route('users.show', Auth::user()) }}"
                                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-t-lg transition">
                                <i class="fas fa-user mr-2"></i>Perfil
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                <i class="fas fa-cog mr-2"></i>Ajustes de perfil
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
                    <div class="hidden lg:flex items-center space-x-1 xl:space-x-2">
                        <a href="{{ route('login') }}"
                            class="px-2.5 py-2 xl:px-4 text-sm font-medium text-gray-400 hover:text-white transition whitespace-nowrap">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-3 py-2 xl:px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition whitespace-nowrap">
                            Registrarse
                        </a>
                    </div>
                @endauth

                <!-- Hamburger Button (Mobile) -->
                <div class="flex items-center lg:hidden ml-2">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-gray-400 hover:text-white focus:outline-none p-2">
                        <i class="fas fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                        <i class="fas fa-times text-xl" x-show="mobileMenuOpen" x-cloak style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-transition class="lg:hidden bg-gray-900 border-t border-gray-800" style="display: none;">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="/" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <i class="fas fa-home mr-2 w-5 text-center"></i>Inicio
            </a>
            <a href="{{ route('media.explore') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('media.explore') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <i class="fas fa-search mr-2 w-5 text-center"></i>Explorar
            </a>
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <i class="fas fa-award mr-2 w-5 text-center"></i>Fiction top
            </a>
            <a href="{{ route('forum.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('forum.index') ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <i class="fas fa-comments mr-2 w-5 text-center"></i>Foro
            </a>
        </div>
        
        <!-- Mobile Search -->
        <div class="px-4 py-4 border-t border-gray-800">
            <form action="{{ url('/search/unified') }}" method="GET" class="w-full">
                <input type="hidden" name="type" value="all">
                <div class="relative w-full">
                    <input type="text" name="query" placeholder="Buscar..." class="w-full bg-gray-800 text-white placeholder-gray-500 rounded-lg py-3 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-500"></i>
                </div>
            </form>
        </div>

        <!-- Mobile User Area -->
        <div class="pt-4 pb-4 border-t border-gray-800">
            @auth
                <div class="flex items-center px-5 mb-4">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->username }}" class="w-10 h-10 rounded-full object-cover">
                    <div class="ml-3">
                        <div class="text-base font-medium leading-none text-white">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium leading-none text-gray-400 mt-1">{{ Auth::user()->username }}</div>
                    </div>
                </div>
                <div class="px-2 space-y-1">
                    <a href="{{ route('users.show', Auth::user()) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-user mr-2 w-5 text-center"></i>Perfil
                    </a>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-cog mr-2 w-5 text-center"></i>Ajustes de perfil
                    </a>
                    <a href="{{ route('user-list.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-800">
                        <i class="fas fa-list mr-2 w-5 text-center"></i>Mi lista
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-400 hover:text-white hover:bg-red-500/20">
                            <i class="fas fa-sign-out-alt mr-2 w-5 text-center"></i>Cerrar sesión
                        </button>
                    </form>
                </div>
            @else
                <div class="px-5 space-y-3">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 text-base font-medium text-white bg-gray-800 hover:bg-gray-700 rounded-lg transition">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-base font-medium rounded-lg transition">
                        Registrarse
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>