@props([
	'hiddenInputs',
	'content'
])

<form {{ $attributes->merge([ 'method' => 'POST', 'class' => "block group rounded-xl max-w-fit"]) }}">
	@csrf
	{{ $hiddenInputs }}
	<button type="submit" class="relative border border-gray-900 rounded-xl cursor-pointer p-0 mb-auto" @disabled( $content->attributes['disabled'] )>
	{{ $content }}
	</button>
</form>
