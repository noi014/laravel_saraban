<div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-none font-semibold">ระบบจัดการเอกสาร</span>
    @auth
            @if (auth()->user()->receivingAgency)
                <span class="text-xs text-gray-500">
                   [{{ auth()->user()->receivingAgency->name }}]
                   [{{ auth()->user()->role }}]
                </span>
            @endif
        @endauth
</div>
{{-- <div class="flex items-center space-x-2">
    <img src="/logo.png" alt="Logo" class="h-8 w-8">
    <div class="flex flex-col leading-tight">
        <span class="font-semibold text-base">ระบบจัดการเอกสาร</span>
        
        @auth
            @if (auth()->user()->receivingAgency)
                <span class="text-xs text-gray-500">
                    [{{ auth()->user()->receivingAgency->name }}]
                </span>
            @endif
        @endauth
    </div>
</div> --}}
