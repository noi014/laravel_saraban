<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
   
 <head>
        @include('partials.head')
      
       <!-- ✅ CSS -->
    @vite('resources/css/app.css')
    @livewireStyles
    </head>
    
    <body class="min-h-screen bg-white dark:bg-zinc-800">
       
    
      
       

      
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
                
            </a>
           
            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    {{-- <flux:navlist.item icon="page" :href="route('articles.index')" :current="request()->routeIs('articles.index')" wire:navigate>{{ __('Articles') }}</flux:navlist.item> --}}
                    <flux:navlist.item icon="arrow-left-circle" :href="route('officialletter.index')" :current="request()->routeIs('officialletter.index')" wire:navigate>{{ __('หนังสือรับ') }}</flux:navlist.item>
                     <flux:navlist.item icon="arrow-right-circle" :href="route('outgoing-letters')" :current="request()->routeIs('outgoing-letters')" wire:navigate>{{ __('หนังสือส่ง') }}</flux:navlist.item>
                  <flux:navlist.item icon="arrow-right-start-on-rectangle" :href="route('sending-letters')" :current="request()->routeIs('sending-letters')" wire:navigate>{{ __('หนังสือส่ง ว.') }}</flux:navlist.item>
                  <flux:navlist.item icon="lock-closed" :href="route('command-manager')" :current="request()->routeIs('command-manager')" wire:navigate>{{ __('คำสั่ง') }}</flux:navlist.item>
                    <flux:navlist.item icon="megaphone" :href="route('announcement-manager')" :current="request()->routeIs('announcement-manager')" wire:navigate>{{ __('ประกาศ') }}</flux:navlist.item>
                   <flux:navlist.item icon="pencil-square" :href="route('memo-manager')" :current="request()->routeIs('memo-manager')" wire:navigate>{{ __('บันทึก') }}</flux:navlist.item>
                  
                   
                    
                    {{-- <flux:navlist.item icon="inbox"  href="#">Inbox</flux:navlist.item> --}}
                    
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />
             @auth
                        @if (auth()->user()->isAdmin())
                        <flux:navlist variant="outline">
                                <flux:navlist.item icon="user" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>{{ __('จัดการผู้ใช้') }}</flux:navlist.item> 
                        </flux:navlist>
                        @endif
                    @endauth
            {{-- <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist> --}}

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
         {{-- @include('components.sweetalert') <!-- ✅ ใส่ component ที่ใช้ SweetAlert2 --> --}}
        {{-- @if (session('swal'))
            <script>
                    Swal.fire(@json(session('swal')));
            </script>
        @endif --}}
        <!-- ต้องโหลด sweetalert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- รอ event จาก Livewire -->
<script>
    window.addEventListener('swal', event => {
        Swal.fire(event.detail);
    });
</script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "การลบข้อมูลจะไม่สามารถกู้คืนได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirm-delete', { id });
            }
        });
    }
</script>

     {{-- @livewireScripts --}}

<!-- ก่อน </body> -->

    {{-- <script defer src="https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js"></script> --}}
        <livewire:scripts />
        <livewire:styles />
        @vite('resources/js/app.js')
        @livewireScripts
    </body>
</html>
