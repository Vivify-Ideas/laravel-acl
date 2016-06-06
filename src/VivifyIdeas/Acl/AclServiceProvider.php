<?php

namespace VivifyIdeas\Acl;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AclServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('vivifyideas/acl.php'),
        ]);

        $provider = $this->getProviderClass();
        $this->app->bind('Acl', function() use ($provider) {
            // default permissions providers is Eloquent provider
            return new Acl(new $provider);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAclInstallCommand();
        $this->registerAclResetCommand();
        $this->registerAclUpdateCommand();
    }

    private function getProviderClass()
    {
        $provider = config('vivifyideas.acl.provider');

        return 'VivifyIdeas\Acl\PermissionProviders\\' . ucfirst($provider) . 'Provider';
    }

    /**
     * Register acl:install command
     */
    protected function registerAclInstallCommand()
    {
        $this->commands([
            \VivifyIdeas\Acl\Commands\InstallCommand::class
        ]);
    }

    /**
     * Register acl:reset command
     */
    protected function registerAclResetCommand()
    {
        $this->commands([
            \VivifyIdeas\Acl\Commands\ResetCommand::class
        ]);
    }

    /**
     * Register acl:update command
     */
    protected function registerAclUpdateCommand()
    {
        $this->commands([
            \VivifyIdeas\Acl\Commands\UpdateCommand::class
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

}
