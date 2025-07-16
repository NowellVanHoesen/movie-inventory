@props([
	'cast' => [],
	'display_limit' => 10,
	'multi_cols' => false
])
<div x-data="{ showAll: false, castToShow: @js($display_limit) }">
	<div class="flex items-center justify-between">
		<h2 class="text-2xl">Cast Members</h2>
		@if ( count( $cast ) > $display_limit )
		<button @click="showAll = !showAll" class="cursor-pointer px-4 py-2 text-sm font-semibold text-white bg-[#3e4b62] border border-transparent rounded-md hover:bg-[#333c50] focus:bg-[#333c50] focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition ease-in-out duration-150">
			<span x-text="showAll ? 'Show Less' : 'Show All'"></span>
		</button>
		@endif
	</div>
	<ul {{ $attributes->class(['mt-4', 'columns-1 gap-4 lg:columns-2' => $multi_cols]) }}class="mt-4">
		@foreach ($cast as $index => $cast_member)
			@continue( str_contains( $cast_member->pivot->character, 'uncredited' ) )

			<li class="flex w-full" x-show="showAll || {{ $index }} < castToShow">
				{{ $cast_member->name }}
				<span class="text-right flex flex-1 before:border-b-gray-500 before:border-b-2 before:border-dotted before:content-[''] before:flex-1 before:mx-1 before:mb-[0.3rem]">{{ $cast_member->pivot->character }}</span>
			</li>
		@endforeach
	</ul>
</div>
