<?php
namespace TonNamespace\AuthCommand;

use Illuminate\Support\ServiceProvider;
use TonNamespace\AuthCommand\Commands\MakeAuthController;

class AuthCommandServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeAuthController::class,
        ]);
    }

    public function boot()
    {
        // Chargement de la commande
    }
}
