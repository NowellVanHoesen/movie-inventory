@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium leading-6 text-gray-50 font-bold']) }}>
    {{ $value ?: $slot }}
</label>
