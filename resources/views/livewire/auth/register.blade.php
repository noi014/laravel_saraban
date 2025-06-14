<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Session; // เพิ่มที่ด้านบนถ้ายังไม่มี
new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    // public function register(): void
    // {
    //     $validated = $this->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
    //         'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    //     ]);

    //     $validated['password'] = Hash::make($validated['password']);

    //     event(new Registered(($user = User::create($validated))));

    //     Auth::login($user);

    //     $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    // }
    public function register(): void
{
    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    ]);

    $validated['password'] = Hash::make($validated['password']);

    // สร้างผู้ใช้ใหม่ (ไม่ login ทันที)
    $user = User::create($validated);

    event(new Registered($user));

    // ส่ง SweetAlert แจ้งเตือนหลังลงทะเบียน
   
     $this->js(<<<JS
    Swal.fire({
        icon: 'success',
        title: 'ลงทะเบียนสำเร็จ',
        text: 'โปรดรอการอนุมัติจากผู้ดูแลระบบ',
        showConfirmButton: false,
        timerProgressBar: true,
        position: 'top-end',
        toast: true,
        timer: 5000,
    }).then(() => {
        window.location.href = '/login';
    });
JS);

    // เปลี่ยนเส้นทางไปหน้า login
    //$this->redirect(route('login'), navigate: true);
}
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('swal', event => {
        Swal.fire(event.detail);
    });
</script>
</div>
