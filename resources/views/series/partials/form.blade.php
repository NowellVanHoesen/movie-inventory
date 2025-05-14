<form method="POST" action="{{ route('series.store') }}">
	@csrf
	<div class="grid grid-cols-[1fr_calc(50%-93px)] gap-4">
		<div class="grid grid-cols-[185px_1fr] gap-4 mt-6 text-gray-900 rounded-xl" style="background-size: contain; background-position: top right; background-image: linear-gradient( rgba(255,255,255,0.8), rgba(255,255,255,0.8)), url(https://image.tmdb.org/t/p/original{{ $series_detail->backdrop_path }})">
			<img src={{ "https://image.tmdb.org/t/p/w185/" . $series_detail->poster_path }} alt="{{ $series_detail->name }} series poster" class="rounded-l-xl">

			<div class="sm:flex sm:flex-col my-4">
				<p class="text-2xl">{{ $series_detail->name }}: <em class="text-base">{{ $series_detail->tagline }}</em></p>
				<p class="text-sm">{{ $series_detail->original_name ?: '' }}</p>
				<p class="text-sm">{{ implode( ' | ',$series_detail->genres ) }}</p>
				<p class="space-x-4 text-sm">
					<span>First Air Date: {{ $series_detail->first_air_date }}</span>
					<span>
						@foreach ( $series_detail->content_ratings->results as $rDate )
							@if ( $rDate->iso_3166_1 !== 'US' )
								@continue
							@endif
							{{ $rDate->rating }}
						@endforeach
					</span>
					{{-- <span>{{ $series_detail->runtime }} min.</span> --}}
				</p>
				<p>{{ ! empty( $series_detail->purchase_date ) ? "Purchase Date: {$series_detail->purchase_date}" : '' }}</p>
				<p class="mt-4">{{ $series_detail->overview }}</p>
			</div>
		</div>
		<div class="mt-6 p-4 rounded-xl bg-white/70">
			<div class="grid gap-4">
				<x-form-field cols="1">
					<x-form-label for="purchase_date" value="Date Purchased" class="text-gray-900" />
					<x-form-input type="date" name="purchase_date" id="purchase_date" />
				</x-form-field>
			</div>
			<div class="grid grid-cols-2">
				<x-form-label class="text-gray-900 col-span-full" value="Media Type" />
				@foreach ($media_types as $type => $sub_types )
				<div>
					<span class="text-gray-900 text-sm font-bold">{{ $type }}</span>
					<ul>
						@foreach ($sub_types as $type_id => $sub_type )
						<li>
							<x-form-field>
								<x-form-label class="text-gray-900 flex place-items-center">
									<x-form-checkbox name="media_type[]" :value="$type_id" /> {{ $sub_type }}
								</x-form-label>
							</x-form-field>
						</li>
						@endforeach
					</ul>
				</div>
				@endforeach
			</div>
		</div>
		<div class="col-start-1 col-span-full">
			@include('series.partials.seasons-form')
		</div>
		<div class="col-start-2 text-right mt-6">
			<x-form-input type="hidden" name="series_id" :value="$series_detail->id" />
			<x-form-button>{{ __('Add Series') }}</x-form-button>
		</div>
	</div>
</form>
