<div x-data="{ showAll: false, castToShow: 10 }">
	<div class="flex items-center justify-between">
		<h2 class="text-2xl">Cast</h2>
		<button @click="showAll = !showAll" class="relative cursor-pointer px-4 py-2 text-sm font-medium text-gray-300 bg-[#3e4b62] border border-gray-300 leading-5 rounded-md hover:bg-gray-800 hover:text-white focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 transition ease-in-out duration-150">
			<span x-text="showAll ? 'Show Less' : 'Show All'"></span>
		</button>
	</div>
	<ul class="mt-4">
		@foreach ($cast as $index => $cast_member)
			@continue( str_contains( $cast_member->pivot->character, 'uncredited' ) )

			<li class="flex w-full" x-show="showAll || {{ $index }} < castToShow">
				{{ $cast_member->name }}
				<span class="text-right flex flex-1 before:border-b-gray-500 before:border-b-2 before:border-dotted before:content-[''] before:flex-1 before:mx-1 before:mb-[0.3rem]">{{ $cast_member->pivot->character }}</span>
			</li>
		@endforeach
	</ul>
</div>
