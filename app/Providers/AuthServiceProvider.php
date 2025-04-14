<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('admin', function ($user) {
            Log::info('Verificando gate admin', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return $user->role === 'admin';
        });

        Gate::define('vendas', function ($user) {
            Log::info('Verificando gate vendas', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return strtolower($user->role) === 'vendas';
        });

        Gate::define('financeiro', function ($user) {
            Log::info('Verificando gate financeiro', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return $user->role === 'financeiro';
        });
    }
} 