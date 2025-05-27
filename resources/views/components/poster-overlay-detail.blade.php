@props([
	'title',
	'release_year',
	'certification' => ''
])

<div {{ $attributes->merge(['class' => "text-gray-900 p-2 absolute bottom-0 left-0 right-0 bg-white/75 rounded-b-xl leading-none"]) }}>
	<p class="font-bold">{{ $title }}</p>
	@if ( ! empty( $release_year ) )
		<p class="text-sm">{{ $release_year }} {{ $certification }}</p>
	@endif
</div>
