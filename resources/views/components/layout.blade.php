@props([ 'page_title' => config('app.name') ])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cold-steel-600">

    <x-head page_title="{{ 'Movie Inventory - ' . $page_title }}" />

    <body class="h-screen flex flex-col">
        <header class="bg-cold-steel-700 shadow-sm">
            <div class="relative text-cold-steel-100 flex flex-row px-4 py-4 place-items-center md:justify-normal h-16">
                @include('layouts.navigation')
            </div>
        </header>
        <main class="flex-1 grow mb-auto text-cold-steel-100 bg-cold-steel-600 bg-cover bg-no-repeat bg-top bg-fixed">
            <div class="mx-auto max-w-7xl p-2 lg:px-6 rounded-md bg-cold-steel-600/75">
                <div class="flex justify-between mx-auto max-w-7xl py-6 text-cold-steel-50 mb-4">
                    <h1 class="text-3xl font-bold tracking-tight">{{ $heading }}</h1>
                </div>
                {{ $slot }}
            </div>
        </main>
        <x-footer class="bg-cold-steel-800" />
    </body>

</html>
