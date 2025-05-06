<nav x-data="{ open: false, movieOpen: false }" class="bg-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <i class="fa-solid fa-clapperboard fa-2xl text-gray-200"></i>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="/" :active="Route::is('home')">Home</x-nav-link>
                    <x-nav-link href="/movies" :active="Route::is('movies.*')">Movies</x-nav-link>
                    <x-nav-link href="/series" :active="Route::is('series.*')">Series</x-nav-link>
                    <x-nav-link href="/about" :active="Route::is('about')">About</x-nav-link>
                    <x-nav-link href="/contact" :active="Route::is('contact')">Contact</x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-2">
                @auth
                    <!-- Profile dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md hover:text-gray-700 bg-gray-800 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden"
                                aria-expanded="false" aria-haspopup="true">
                                <div class="ms-1">
                                    <img class="size-8 rounded-full"
                                        src="https://gravatar.com/avatar/{{  hash( 'sha256', Auth::user()->email ) }}"
                                        alt="">
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                              this.closest('form').submit();">Log
                                    Out</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <x-nav-link :href="route('login')" :active="request()->routeIs('login')">Log in</x-nav-link>

                    @if (Route::has('register'))
                        <x-nav-link :href="route('register')" :active="request()->routeIs('register')">Register</x-nav-link>
                    @endif
                @endauth
            </div>
            <div class="-me-2 flex items-center sm:hidden">
                <!-- Mobile menu button -->
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div :class="{ 'block': open, 'hidden': !open }" class="sm:hidden hidden">
        <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            <a href="/" class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white"
                aria-current="page">Home</a>
            <a href="/about"
                class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">About</a>
            <a href="/contact"
                class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Contact</a>
        </div>
        <div class="border-t border-gray-700 pt-4 pb-3">
            @auth

                <div class="flex justify-between items-center px-5">
                    <div class="flex">
                        <div class="shrink-0">
                            <img class="size-10 rounded-full"
                                src="https://gravatar.com/avatar/{{ hash('sha256', Auth::user()->email) }}"
                                alt="{{ Auth::user()->name }} gravitar">
                        </div>
                        <div class="ml-3">
                            <div class="text-base/5 font-medium text-white">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div>
                        <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">Log Out
                            </x-responsive-nav-link>
                        </form>

                    </div>
                </div>
            @else
                <div class="flex justify-end items-center px-5">
                    <div>
                        <x-responsive-nav-link :href="route('login')">Log In</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
                    </div>
                </div>

            @endauth
        </div>
    </div>
</nav>
