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
		$this->package('vivify-ideas/acl');

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

		$this->commands('acl.install', 'acl.reset', 'acl.update');
	}

	private function getProviderClass()
	{
		$provider = Config::get('acl::provider');
		return 'VivifyIdeas\Acl\PermissionProviders\\' . ucfirst($provider) . 'Provider';
	}

	/**
	 * Register acl:install command
	 */
	protected function registerAclInstallCommand()
	{
	    $this->app['acl.install'] = $this->app->share(function($app) {
	        return new Commands\InstallCommand();
	    });
	}

	/**
	 * Register acl:reset command
	 */
	protected function registerAclResetCommand()
	{
	    $this->app['acl.reset'] = $this->app->share(function($app) {
	        return new Commands\ResetCommand();
	    });
	}

	/**
	 * Register acl:update command
	 */
	protected function registerAclUpdateCommand()
	{
	    $this->app['acl.update'] = $this->app->share(function($app) {
	        return new Commands\UpdateCommand();
	    });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
