@props(['placeholder' => false, 'poster_path', 'size' => 'w154'])

@php
$max_w = match ($size) {
	'w92' => '92px',
	'w154' => '156px',
	default => '185px',
}
@endphp

<img src="{{ $placeholder ? config('tmdb.placeholder.poster') : 'https://image.tmdb.org/t/p/' . $size . $poster_path }}" class="border border-cold-steel-900 rounded-xl w-full max-w-[{{ $max_w }}]" />
