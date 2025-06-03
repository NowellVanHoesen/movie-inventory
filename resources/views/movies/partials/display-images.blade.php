<div class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(300px,1fr))] gap-4">
	@foreach ($images->backdrops as $backbrop)
		<a href="{{ "https://image.tmdb.org/t/p/original" . $backbrop->file_path }}" target="_blank">
			<img src={{ "https://image.tmdb.org/t/p/w300" . $backbrop->file_path }} alt="{{ $movie->title }} movie poster">
		</a>
	@endforeach
</div>
<div class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(185px,1fr))] gap-4">
	@foreach ($images->posters as $poster)
		<a href="{{ "https://image.tmdb.org/t/p/original" . $poster->file_path }}" target="_blank">
			<img src={{ "https://image.tmdb.org/t/p/w185" . $poster->file_path }} alt="{{ $movie->title }} movie poster">
		</a>
	@endforeach
</div>