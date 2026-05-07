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
        <link rel="icon" type="image/svg+xml" href="{{ asset('film-slate_3171927.png') }}">
        <title>{{ $page_title }}</title>
        @routes
        @vite(["resources/css/app.css", "resources/js/app.js"])
        @inertiaHead
    </head>

    <body class="flex h-screen flex-col">
        @inertia
    </body>
</html>
