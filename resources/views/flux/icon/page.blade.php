{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
    'variant' => 'outline',
])

@php
    if ($variant === 'solid') {
        throw new \Exception('The "solid" variant is not supported in Lucide.');
    }

    $classes = Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        },
    );

    $strokeWidth = match ($variant) {
        'outline' => 2,
        'mini' => 2.25,
        'micro' => 2.5,
    };
@endphp

<svg
{{ $attributes->class($classes) }}
xmlns="http://www.w3.org/2000/svg" 
width="24" height="24" viewBox="0 0 24 24" 
fill="none" stroke="currentColor" 
stroke-width="{{ $strokeWidth }}" stroke-linecap="round" 
stroke-linejoin="round" 
class="lucide lucide-sticky-note-icon lucide-sticky-note">
<path d="M16 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8Z"/>
<path d="M15 3v4a2 2 0 0 0 2 2h4"/>
</svg>