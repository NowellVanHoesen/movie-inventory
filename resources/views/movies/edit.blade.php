<x-movies-layout main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $movie->backdrop_path }})">
	<div class="bg-white/80 text-gray-900 mt-6 p-6 rounded-xl md:grid md:grid-cols-[300px_1fr] gap-4">
		<div>
			<img src={{ "https://image.tmdb.org/t/p/w300/" . $movie->poster_path }} alt="{{ $movie->title }} movie poster">
		</div>
		<div>
			<h2 class="text-2xl">
				{{ $movie->title }}
				<span class="text-sm font-normal">( {{ $movie->certification->name }} ) {{ $movie->runtime }} min.</span>
			</h2>
			<p><em>{{ $movie->tagline }}</em></p>
			<div class="sm:flex sm:justify-between sm:items-start">
				<div>
					<p class="text-sm">{{ $movie->genres->pluck('name')->join(' | ') }}</p>
				</div>
			</div>
			<p class="mt-4">{{ $movie->overview }}</p>
			<div class="m-4">
				<form method="POST" action="{{ route('movies.update', $movie) }}">
					@csrf
					@method('PATCH')
					<x-form-hidden-input name="movie_id" value="{{ $movie->id }}" />
					<x-form-field>
						<x-form-label for="purchase_date" value="Date Purchased" class="text-gray-900" />
						<x-form-input type="date" name="purchase_date" id="purchase_date" value="{{ $movie->purchase_date }}" />
					</x-form-field>
					<x-form-label class="text-gray-900" value="Media Type" />
					<div class="grid md:grid-cols-3 grid-cols-2">
						@foreach ($media_types as $type => $sub_types )
							<div>
								<span class="text-sm font-bold">{{ $type }}</span>
								<ul>
									@foreach ($sub_types as $type_id => $sub_type )
										<li>
											<x-form-field>
												<x-form-label class="text-gray-900 flex place-items-center">
													<x-form-checkbox name="media_type[]" value="{{ $type_id }}" :checked="$movie->media_types->contains($type_id)" /> {{ $sub_type }}
												</x-form-label>
											</x-form-field>
										</li>
									@endforeach
								</ul>
							</div>
						@endforeach
					</div>
					<div class="flex items-center justify-end mt-6 gap-x-6 mx-auto">
						<x-form-button>{{ __('Update Movie') }}</x-form-button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-movies-layout>
