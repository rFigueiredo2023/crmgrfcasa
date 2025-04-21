<?php

namespace App\Providers;

use App\Http\Livewire\DevAssistant;
use Illuminate\Support\ServiceProvider;
// use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Comentado porque o pacote Livewire não está instalado
        // Livewire::component('dev-assistant', DevAssistant::class);
    }
}
