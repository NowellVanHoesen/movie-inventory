@props(['active' => false])

<a
	class="{{ $active ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-[#3e4b62] hover:text-white' }} block px-3 py-2 text-sm font-medium text-gray-300 hover:bg-[#333C50] hover:text-white rounded-md"
	aria-current="{{ $active ? 'page' : 'false' }}"
	{{ $attributes }}
>{{ $slot }}</a>
