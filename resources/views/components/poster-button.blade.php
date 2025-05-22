@props([
	'hiddenInputs',
	'content'
])

<form {{ $attributes->merge([ 'method' => 'POST', 'class' => "block relative group border border-gray-900 rounded-xl max-w-fit mb-auto"]) }}">
	@csrf
	{{ $hiddenInputs }}
	<button type="submit" class="cursor-pointer p-0 mb-auto" @disabled( $content->attributes['disabled'] )>
	{{ $content }}
	</button>
</form>
