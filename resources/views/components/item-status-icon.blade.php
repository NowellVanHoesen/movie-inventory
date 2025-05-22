@props(['purchased' => false])

<i class="absolute top-4 right-2 fa-solid fa-lg text-shadow-lg/30 {{ $purchased ? 'fa-circle-check text-[#bada55]' : 'fa-heart text-[#f5131a]' }}"></i>
