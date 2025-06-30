@props(['active' => false])

<a
	class="{{ $active ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block md:inline-block rounded-md md:px-3 md:py-2 px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white"
	aria-current="{{ $active ? 'page' : 'false' }}"
	{{ $attributes }}
>{{ $slot }}</a>
