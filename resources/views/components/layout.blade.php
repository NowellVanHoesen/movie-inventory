@props([ 'page_title' => config('app.name') ])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">

    <x-head page_title="{{ 'Movie Inventory - ' . $page_title }}" />

    <body class="h-screen flex flex-col">
        <header class="bg-[#333C50] shadow-sm">
            <div class="relative bg-gray-800 text-gray-200 flex flex-row px-4 py-4 place-items-center md:justify-normal h-16">
                @include('layouts.navigation')
            </div>
            <div class="flex justify-between mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 text-gray-50">
                <h1 class="text-3xl font-bold tracking-tight">{{ $heading }}</h1>
            </div>
        </header>
        <main class="flex-1 grow mb-auto text-gray-50 bg-[#3e4b62] bg-cover bg-no-repeat bg-top bg-fixed">
            <div class="mx-auto max-w-7xl p-2 lg:px-6">
                {{ $slot }}
            </div>
        </main>
        <x-footer class="bg-gray-800" />
    </body>

</html>
