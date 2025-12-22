@props(['active' => false])

<a
	class="bg-cold-steel-600 block px-3 py-2 text-sm font-medium text-cold-steel-100 hover:bg-cold-steel-800 hover:text-cold-steel-50 focus:ring focus:border-blue-300 rounded-md"
	{{ $attributes }}
>{{ $slot }}</a>
