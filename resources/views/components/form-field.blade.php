@props(['cols' => '4'])

@php
$col_span = match ($cols) {
    '4' => 'sm:col-span-4',
    default => "sm:col-span-{$cols}",
};
@endphp

<div {{ $attributes->merge([ 'class' => $col_span ]) }}>
    {{ $slot }}
</div>
