<div class="grid grid-cols-1 md:grid-cols-[185px_1fr] gap-4 mt-6 bg-white/80 rounded-xl mb-auto overflow-hidden">
	<img src={{ "https://image.tmdb.org/t/p/w185/" . $series->poster_path }} alt="{{ $series->name }} series poster" class="tl-xl">

	<div class="sm:flex sm:flex-col m-4 md:ml-0">
		<p class="text-2xl leading-none">
			{{ $series->name }}
			<span class="text-sm">({{ $series->certification->name }})</span>
		</p>
		<p><em class="text-base">{{ $series->tagline }}</em></p>
		@if ( ! empty( $series->original_name ) && $series->original_name !== $series->name )
			<p class="text-sm">{{ $series->original_name }}</p>
		@endif
		<p class="text-sm mb-2">
			{{ $series->genres->pluck('name')->join(' | ') }}
		</p>
		@if ( is_null($series->purchase_date) )<p class="text-sm font-normal text-[#3e4b62]">wishlist</p>@endif
		@foreach ($series->media_types_display as $parent => $media_types)
			<p class="text-sm"><strong>{{ $parent }}</strong>: {{ implode(' | ', $media_types) }}</p>
		@endforeach
		<p class="text-sm"><strong>First Air Date</strong>: {{ $series->first_air_date }}</p>
		<p class="mt-4">{{ $series->overview }}</p>
	</div>
</div>