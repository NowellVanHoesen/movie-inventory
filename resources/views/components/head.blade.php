@props(['page_title' => 'Movie Inventory'])
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page_title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
