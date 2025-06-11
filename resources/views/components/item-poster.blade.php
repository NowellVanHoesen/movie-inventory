@props(['placeholder' => false, 'poster_path', 'size'])

@php
$max_w = match ($size) {
	'w92' => '92px',
	'w154' => '154px',
	default => '185px',
}
@endphp

<img src="{{ $placeholder ? config('tmdb.placeholder.poster') : 'https://image.tmdb.org/t/p/' . $size . $poster_path }}" class="border border-gray-900 rounded-xl max-w-[{{ $max_w }}]" />
