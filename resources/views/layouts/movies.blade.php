@props([ 'main_bg_style' => '', 'page_title' => 'Movie Inventory - Movies' ])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cold-steel-600">

    <x-head page_title="{{ $page_title }}" />

    <body class="h-screen flex flex-col">
        <header class="bg-cold-steel-700 shadow-sm z-10">
            <div class="relative text-cold-steel-200 flex flex-row px-4 py-4 place-items-center md:justify-normal h-16">
                @include('layouts.navigation')
            </div>
        </header>
        <main class="flex-1 grow mb-auto text-cold-steel-100 bg-cold-steel-600 bg-cover bg-no-repeat bg-top bg-fixed"
            style="{{ $main_bg_style }}">
            <div class="mx-auto max-w-7xl p-2 lg:px-6">
                <div class="p-6 flex justify-between mx-auto max-w-7xl rounded-md bg-cold-steel-600/75 text-cold-steel-50">
                    <div class="flex items-center">
                        <div class="flex flex-wrap md:flex-nowrap items-center gap-4">
                            <h1 class="text-3xl font-bold tracking-tight">Movies</h1>
                            @auth
                                <div class="grow place-content-center sm:space-x-4 relative"><a href="{{ route('movies.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md focus:outline-none focus:ring transition ease-in-out duration-150 text-cold-steel-100 active:text-cold-steel-300 hover:text-cold-steel-50 bg-cold-steel-800 active:bg-cold-steel-700 hover:bg-cold-steel-900 ring-cold-steel-300 border-cold-steel-600 focus:border-blue-700">Add Movie</a></div>
                            @endauth
                        </div>
                    </div>
                    @if(Route::is('movies.index'))
                    <x-nav aria-label="movie filter" class="flex flex-wrap md:flex-nowrap md:space-x-4">
                        <x-nav-alt-link href="{{ route('movies.index') }}" :active="Route::is('movies.index') && request()->missing(['wishlist']) && request()->missing(['purchased'])">All</x-nav-alt-link>
                        <x-nav-alt-link href="{{ route('movies.index', ['purchased']) }}" :active="request()->has('purchased')">Purchased</x-nav-alt-link>
                        <x-nav-alt-link href="{{ route('movies.index', ['wishlist']) }}" :active="request()->has('wishlist')">Wishlist</x-nav-alt-link>
                    </x-nav>
                    @endif
                </div>
                {{ $slot }}
            </div>
        </main>
        <x-footer class="bg-cold-steel-800" />
    </body>

</html>
