@props(["page_title" => config("app.name")])
<!DOCTYPE html>
<html
    lang="{{ str_replace("_", "-", app()->getLocale()) }}"
    class="bg-cold-steel-600 h-full"
>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ $page_title }}</title>

        @vite(["resources/css/app.css", "resources/js/app.js"])
        @inertiaHead
    </head>

    <body class="flex h-screen flex-col">
        <header class="bg-cold-steel-700 shadow-sm">
            <div
                class="text-cold-steel-100 relative flex h-16 flex-row place-items-center px-4 py-4 md:justify-normal"
            >
                {{-- @include("layouts.navigation") --}}
            </div>
        </header>
        <main
            class="text-cold-steel-100 bg-cold-steel-600 mb-auto flex-1 grow bg-cover bg-fixed bg-top bg-no-repeat"
        >
            <div
                class="bg-cold-steel-600/75 mx-auto max-w-7xl rounded-md p-2 lg:px-6"
            >
                <div
                    class="text-cold-steel-50 mx-auto mb-4 flex max-w-7xl justify-between py-6"
                >
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{-- {{ $heading }} --}}
                    </h1>
                </div>
                @inertia
            </div>
        </main>
        <x-footer class="bg-cold-steel-800" />
    </body>
</html>
