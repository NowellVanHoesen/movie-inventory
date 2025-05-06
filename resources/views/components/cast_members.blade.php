<p>Cast</p>
<ul class="mt-4">
	@foreach ($cast as $cast_member)
		@continue( str_contains( $cast_member->pivot->character, 'uncredited' ) )

		<li class="flex w-full">
			{{ $cast_member->name }}
			<span class="text-right flex flex-1 before:border-b-gray-500 before:border-b-2 before:border-dotted before:content-[''] before:flex-1 before:mx-1 before:mb-[0.3rem]">{{ $cast_member->pivot->character }}</span>
		</li>
	@endforeach
</ul>
