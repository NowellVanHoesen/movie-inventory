<div class="grid grid-cols-[400px_1fr] gap-4 mt-6 text-gray-900" style="background-size: contain; background-position: top right; background-image: linear-gradient( rgba(255,255,255,0.8), rgba(255,255,255,0.8)), url(https://image.tmdb.org/t/p/original{{ $movie->backdrop_path }})">
	<div><img src={{ "https://image.tmdb.org/t/p/w400/" . $movie->poster_path }} alt="{{ $movie->title }} movie poster"></div>

	<div class="sm:flex sm:flex-col sm:justify-between">
		<div>
            <p>{{ $movie->title }}</p>
            @if ( $movie->title !== $movie->original_title )
                <p><strong>Original Title</strong>:( {{ $movie->original_title }} )</p>
            @endif
            <p><em>{{ $movie->tagline }}</em></p>
            <p>{{ implode( ' | ',$movie->genres ) }}</p>
            <p class="space-x-4">
                <span>{{ $movie->release_date }}</span>
                <span>
                    @foreach ( $movie->release_dates->results as $rDate )
                        @if ( $rDate->iso_3166_1 !== 'US' )
                            @continue
                        @endif
                        {{ $rDate->release_dates[0]->certification }}
                    @endforeach
                </span>
                <span>{{ $movie->runtime }} min.</span>
            </p>
            <p>{{ $movie->overview }}</p>
        </div>
        <div class="m-4">
            <form method="POST" action="{{ route('movies.store') }}">
                @csrf

                <x-form-hidden-input name="movie_id" value="{{ $movie->id }}" />

                <div class="grid grid-cols-4 gap-4">
                    <div class="col-span-2">
                        <x-form-label for="purchase_date" value="Date Purchased" class="text-gray-900" />
                    </div>
                    <div class="col-span-2">
                        <x-form-label class="text-gray-900" value="Media Type" />
                    </div>
                    <div class="col-span-2">
                        <x-form-input type="date" name="purchase_date" id="purchase_date" />
                    </div>

                    @foreach ($media_types as $type => $sub_types )
                        <div>
                            <span class="text-sm font-bold">{{ $type }}</span>
                            <ul>
                                @foreach ($sub_types as $type_id => $sub_type )
                                    <li>
                                        <x-form-label class="text-gray-900 flex place-items-center">
                                            <x-form-checkbox name="media_type[]" :value="$type_id" /> {{ $sub_type }}
                                        </x-form-label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-end mt-6 gap-x-6 max-w-xl mx-auto">
                    <x-form-button>{{ __('Add Movie') }}</x-form-button>
                </div>
            </form>
        </div>
	</div>
</div>
