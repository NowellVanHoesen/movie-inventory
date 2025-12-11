@props(['active' => false])

<a
	class="{{ $active ? 'bg-cold-steel-900' : 'bg-cold-steel-800' }} block md:inline-block rounded-md md:px-3 md:py-2 px-4 py-3 text-sm font-medium text-cold-steel-100 hover:bg-cold-steel-700 hover:text-cold-steel-50"
	aria-current="{{ $active ? 'page' : 'false' }}"
	{{ $attributes }}
>{{ $slot }}</a>
