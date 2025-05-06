<div class="grid gap-4 grid-cols-[repeat(auto-fill,minmax(225px,1fr))] mt-6 text-gray-900 rounded-xl p-4">
	<p class="col-span-full text-xl text-gray-200">Seasons</p>
	@foreach ($series_detail->seasons as $season)
		@include('series.partials.season-checkbox')
	@endforeach
</div>