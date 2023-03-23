<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->registerPolicies();
        Gate::define('pjm', function (User $user) {
            return $user->role_id === 1;
        });

        Gate::define('kajur', function (User $user) {
            return $user->role_id === 2;
        });

        Gate::define('koorprodi', function (User $user) {
            return $user->role_id === 3;
        });

        Gate::define('auditor', function (User $user) {
            return $user->role_id === 4;
        });
    }
}