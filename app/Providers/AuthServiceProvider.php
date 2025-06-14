<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // กำหนดสิทธิ์ admin
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
