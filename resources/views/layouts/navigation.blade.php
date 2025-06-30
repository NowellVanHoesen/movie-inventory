<div x-data="{ mobileOpen: false }" class="left-[-4px] w-full md:max-w-7xl md:mx-auto px-4 sm:px-6 lg:px-8 flex justify-between md:justify-normal">
    <!-- Logo -->
    <a href="{{ route('home') }}" class="place-self-center">
        <i class="fa-solid fa-clapperboard fa-2xl" title="Movie Inventory"></i>
    </a>
    <!-- Mobile menu button -->
    <button @click="mobileOpen = ! mobileOpen" class="h-10 w-10 md:hidden p-2 text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-dropdown" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <i :class="{ 'fa-xmark': mobileOpen, 'fa-bars': !mobileOpen }" class="fa-solid fa-xl"></i>
    </button>
    <div :class="{ 'block': mobileOpen, 'hidden': !mobileOpen }" class="z-15 absolute top-full left-0 right-0 md:relative md:flex md:flex-row md:items-center md:justify-between md:w-full px-2 rounded-b-lg md:rounded-none drop-shadow-lg md:shadow-none bg-gray-900 md:bg-transparent">
        <!-- Primary Navigation Menu -->
        <x-nav aria-label="main" class="md:grow-1 md:space-x-4 md:flex">
            <x-nav-link href="{{ route('home') }}" :active="Route::is('home')">Home</x-nav-link>
            <x-nav-link href="{{ route('movies.index') }}" :active="Route::is('movies.*')">Movies</x-nav-link>
            <x-nav-link href="{{ route('series.index') }}" :active="Route::is('series.*')">Series</x-nav-link>
            <x-nav-link href="{{ route('about') }}" :active="Route::is('about')">About</x-nav-link>
            <x-nav-link href="{{ route('contact') }}" :active="Route::is('contact')">Contact</x-nav-link>
        </x-nav>
        <div class="md:ml-6 my-2 px-2 flex flex-1 md:justify-end">
            <form method="GET" action="{{ route('search') }}" class="w-full">
                @csrf
                <div class="grid grid-cols-[repeat(1,minmax(0,1fr))] md:max-w-80 max-w-full w-full min-w-45 shadow-sm rounded-md ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                    <input name="search" type="search" value="{{ request('search', '') }}" placeholder="Search" aria-label="Search" class="outline-none py-1.5 pl-10 pr-3 border-0 w-full block row-start-1 col-start-1 bg-gray-50 text-gray-900 placeholder:text-gray-400 rounded-md focus:ring-0 sm:text-sm sm:leading-6" />
                    <i class="fa-solid fa-magnifying-glass text-[#99a1af] self-center row-start-1 col-start-1 block align-middle leading-[24px] w-5 h-5 ml-3 pointer-events-none"></i>
                </div>
                <x-form-button class="hidden"></x-form-button>
            </form>
        </div>
        <!-- Settings Dropdown -->
        <x-nav aria-label="profile" class="grow-0 flex flex-row space-x-4 justify-end">
            @auth
                <!-- Profile dropdown -->
                <x-dropdown align="right">
                    <x-slot:trigger>
                        <button
                            class="inline-flex items-center px-3 py-2 group"
                            aria-expanded="false" aria-haspopup="true">
                            <div class="ms-1">
                                <img class="size-10 ring-transparent ring-2 rounded-full group-hover:size-11 group-hover:ring-blue-500 group-focus:ring-blue-500 group-focus:size-11"
                                    src="https://gravatar.com/avatar/{{  hash( 'sha256', Auth::user()->email ) }}"
                                    alt="">
                            </div>
                        </button>
                    </x-slot>
                    <x-slot:content>
                        <x-nav-alt-link href="{{ route('dashboard') }}" :active="Route::is('dashboard')">Dashboard</x-nav-alt-link>
                        <x-nav-alt-link href="{{ route('profile.edit') }}">Profile</x-nav-alt-link>
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-nav-alt-link href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                            this.closest('form').submit();">Log Out</x-nav-alt-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <x-nav-link href="{{ route('login') }}" :active="Route::is('login')">Log in</x-nav-link>
                @if (Route::has('register'))
                    <x-nav-link href="{{ route('register') }}" :active="Route::is('register')">Register</x-nav-link>
                @endif
            @endauth
        </x-nav>
    </div>
</div>
