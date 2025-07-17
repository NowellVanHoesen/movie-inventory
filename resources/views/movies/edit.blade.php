<x-movies-layout page_title="{{ $page_title ?? 'Movie Inventory' }}" main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $movie->backdrop_path }})">
	<div class="bg-white/80 text-gray-900 mt-6 p-6 rounded-xl md:grid md:grid-cols-[300px_1fr] gap-4">
		<div>
			<img src={{ "https://image.tmdb.org/t/p/w300" . $movie->poster_path }} alt="{{ $movie->title }} movie poster">
		</div>
		<div class="sm:flex sm:flex-col sm:justify-between">
			<div>
				<h2 class="text-2xl">
					{{ $movie->title }}
					<span class="text-sm font-normal">( {{ $movie->certification->name }} ) {{ $movie->runtime }} min.</span>
				</h2>
				@if ( $movie->title !== $movie->original_title )
							<p><strong>Original Title</strong>:( {{ $movie->original_title }} )</p>
						@endif
				<p><em>{{ $movie->tagline }}</em></p>
				<p class="text-sm">{{ $movie->genres->pluck('name')->join(' | ') }}</p>
				<p class="mt-4">{{ $movie->overview }}</p>
			</div>
			<div class="m-4">
				<form method="POST" action="{{ route('movies.update', $movie) }}">
					@csrf
					@method('PATCH')
					<x-form-hidden-input name="movie_id" value="{{ $movie->id }}" />
					<div class="grid grid-flow-col grid-cols-2 gap-12">
						<x-form-field cols="1" class="sm:col-start-1">
							<x-form-label for="purchase_date" value="Date Purchased" class="text-gray-900" />
							<x-form-input type="date" name="purchase_date" id="purchase_date" value="{{ $movie->purchase_date }}" />
						</x-form-field>
						<x-form-field cols="2" class="sm:col-start-2">
							<x-form-label class="text-gray-900" value="Media Type" />
							<div class="grid sm:grid-cols-2">
								@foreach ($media_types as $type => $sub_types )
									<div>
										<span class="text-sm font-bold">{{ $type }}</span>
										<ul>
											@foreach ($sub_types as $type_id => $sub_type )
												<li>
													<x-form-label class="text-gray-900 flex place-items-center">
														<x-form-checkbox name="media_type[]" value="{{ $type_id }}" :checked="$movie->media_types->contains($type_id)" /> {{ $sub_type }}
													</x-form-label>
												</li>
											@endforeach
										</ul>
									</div>
								@endforeach
							</div>
						</x-form-field>
					</div>
					<div class="flex items-center justify-end mt-6 gap-x-6 max-w-full">
						<x-form-button>{{ __('Update Movie') }}</x-form-button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-movies-layout>
