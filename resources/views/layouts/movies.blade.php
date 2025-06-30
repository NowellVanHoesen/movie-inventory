@props([ 'main_bg_style' => '' ])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">

    <x-head page_title="Movie Inventory" />

    <body class="h-screen flex flex-col">
        <header class="bg-[#333C50] shadow-sm z-10">
            <div class="relative bg-gray-800 text-gray-200 flex flex-row px-4 py-4 place-items-center md:justify-normal h-16">
                @include('layouts.navigation')
            </div>
            <div class="flex justify-between mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 text-gray-50">
                <div class="flex items-center">
                    <div class="flex flex-wrap md:flex-nowrap items-center">
                        <h1 class="text-3xl font-bold tracking-tight basis-full md:basis-auto">Movies</h1>
                        <x-nav aria-label="main" class="grow-1 md:space-x-4 flex flex-wrap md:flex-nowrap">
                            <x-nav-link href="{{ route('movies.index') }}" :active="Route::is('movies.index')">All</x-nav-link>
                            <x-nav-link href="{{ route('movies.purchased') }}" :active="Route::is('movies.purchased')">Purchased</x-nav-link>
                            <x-nav-link href="{{ route('movies.wishlist') }}" :active="Route::is('movies.wishlist')">Wishlist</x-nav-link>
                            <x-nav-link href="{{ route('movieCollection.index') }}" :active="Route::is('movieCollection.index')">Collections</x-nav-link>
                        </x-nav>
                    </div>
                </div>
                @auth
                    <div class="place-content-center"><a href="{{ route('movies.create') }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-white hover:border-gray-50 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-blue-700 dark:active:bg-gray-700 dark:active:text-gray-300">Add Movie</a></div>
                @endauth
            </div>
        </header>
        <main class="flex-1 grow mb-auto text-gray-50 bg-[#3e4b62] bg-cover bg-no-repeat bg-top bg-fixed"
            style="{{ $main_bg_style }}">
            <div class="mx-auto max-w-7xl p-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
        <x-footer class="bg-gray-800" />
    </body>

</html>
