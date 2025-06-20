<meta charset="utf-8" />
<meta 
{{-- name="viewport" content="width=device-width, initial-scale=1.0"  --}}
name="csrf-token" content="{{ csrf_token() }}"
/>

<title>{{ $title ?? config('app.name') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

 
{{-- <script defer src="//unpkg.com/alpinejs" ></script> --}}


{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
{{-- @livewireStyles --}}

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
