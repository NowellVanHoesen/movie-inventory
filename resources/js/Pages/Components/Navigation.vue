<template>
    <header class="bg-cold-steel-700 shadow-sm">
        <div
            class="text-cold-steel-100 relative flex h-16 flex-row place-items-center px-4 py-4 md:justify-normal"
        >
		<!-- TODO: fix menu toggle -->
            <div
                class="-left-1 flex w-full justify-between px-4 sm:px-6 md:mx-auto md:max-w-7xl md:justify-normal lg:px-8"
            >
                <!-- Logo -->
                <a href="route('home')" class="place-self-center">
                    <i
                        class="fa-solid fa-clapperboard fa-2xl"
                        title="Movie Inventory"
                    ></i>
                </a>
                <!-- Mobile menu button -->
                <button
                    class="h-10 w-10 rounded-lg p-2 text-gray-700 hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 focus:outline-none md:hidden dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-dropdown"
                    aria-expanded="false"
                >
                    <span class="sr-only">Open main menu</span>
                    <i class="fa-solid fa-xl"></i>
                </button>
                <div
                    class="absolute top-full right-0 left-0 z-15 rounded-b-lg px-2 drop-shadow-lg md:relative md:flex md:w-full md:flex-row md:items-center md:justify-between md:rounded-none md:shadow-none"
                >
                    <!-- Primary Navigation Menu -->
                    <x-nav
                        aria-label="main"
                        class="md:flex md:grow md:space-x-4"
                    >
                        <x-nav-link
                            href="route('home')"
                            active="Route::is('home')"
                            >Home</x-nav-link
                        >
                        <x-nav-link
                            href="route('movies.index')"
                            active="Route::is('movies.*')"
                            >Movies</x-nav-link
                        >
                        <x-nav-link
                            href="route('movieCollection.index')"
                            active="Route::is('movieCollection.*')"
                            >Movie Collections</x-nav-link
                        >
                        <x-nav-link
                            href="route('series.index')"
                            active="Route::is('series.*')"
                            >Series</x-nav-link
                        >
                    </x-nav>
                    <div class="my-2 flex flex-1 px-2 md:ml-6 md:justify-end">
                        <form
                            method="GET"
                            action="{{ route('search') }}"
                            class="w-full"
                        >
                            @csrf
                            <div
                                class="grid w-full max-w-full min-w-45 grid-cols-1 rounded-md shadow-sm ring-1 ring-gray-300 ring-inset focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-inset md:max-w-80"
                            >
                                <input
                                    name="search"
                                    type="search"
                                    value="{{ request('search', '') }}"
                                    placeholder="Search"
                                    aria-label="Search"
                                    class="placeholder:text-cold-steel-200 col-start-1 row-start-1 block w-full rounded-md border-0 bg-gray-50 py-1.5 pr-3 pl-10 text-gray-900 outline-none focus:ring-0 sm:text-sm sm:leading-6"
                                />
                                <i
                                    class="fa-solid fa-magnifying-glass text-cold-steel-200 pointer-events-none col-start-1 row-start-1 ml-3 block h-5 w-5 self-center align-middle leading-6"
                                ></i>
                            </div>
                            <x-form-button class="hidden"></x-form-button>
                        </form>
                    </div>
                    <!-- Settings Dropdown -->
                    <x-nav
                        aria-label="profile"
                        class="flex grow-0 flex-row justify-end space-x-4"
                    >
                        <!-- TODO: show when logged in -->
                        <!-- Profile dropdown -->
                        <x-dropdown align="right">
                            <x-slot:trigger>
                                <button
                                    class="group inline-flex items-center px-3 py-2"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                >
                                    <div class="ms-1">
                                        <img
                                            class="size-10 rounded-full ring-2 ring-transparent group-hover:size-11 group-hover:ring-blue-500 group-focus:size-11 group-focus:ring-blue-500"
                                            src="https://gravatar.com/avatar/{{  hash( 'sha256', Auth::user()->email ) }}"
                                            alt=""
                                        />
                                    </div>
                                </button>
                            </x-slot:trigger>
                            <x-slot:content>
                                <x-nav-alt-link
                                    href="route('dashboard')"
                                    active="Route::is('dashboard')"
                                    >Dashboard</x-nav-alt-link
                                >
                                <x-nav-alt-link href="route('profile.edit')"
                                    >Profile</x-nav-alt-link
                                >
                                <!-- Authentication -->
                                <form method="POST" action="route('logout')">
                                    <x-nav-alt-link
                                        href="route('logout')"
                                        onclick="
                                            event.preventDefault();
                                            this.closest('form').submit();
                                        "
                                        >Log Out</x-nav-alt-link
                                    >
                                </form>
                            </x-slot:content>
                        </x-dropdown>
                        <!-- OR -->
                        <x-nav-link
                            href="route('login')"
                            active="Route::is('login')"
                            >Log in</x-nav-link
                        >
                        @if (Route::has('register'))
                        <x-nav-link
                            href="route('register')"
                            active="Route::is('register')"
                            >Register</x-nav-link
                        >
                        @endif
                        <!-- end of auth check -->
                    </x-nav>
                </div>
            </div>
        </div>
    </header>
</template>
