@props(['active' => false])

<a
	class="{{ $active ? 'bg-cold-steel-800' : 'bg-cold-steel-600 hover:text-cold-steel-50' }} block px-3 py-2 text-sm font-medium text-cold-steel-100 hover:bg-cold-steel-700 hover:text-cold-steel-50 rounded-md"
	aria-current="{{ $active ? 'page' : 'false' }}"
	{{ $attributes }}
>{{ $slot }}</a>
