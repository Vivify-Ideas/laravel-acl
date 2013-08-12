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
		    return new Checker(new $provider);
		});

		$this->app->bind('AclManager', function() use ($provider) {
		    // default permissions providers is Eloquent provider
		    return new Manager(new $provider);
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
		$this->registerAclManageCommand();

		$this->commands('acl.install', 'acl.manage');
	}

	private function getProviderClass()
	{

		if (($provider = Config::get('acl::provider')) === null) {
			$provider = 'test';
		}

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
	 * Register acl:manage command
	 */
	protected function registerAclManageCommand()
	{
	    $this->app['acl.manage'] = $this->app->share(function($app) {
	        return new Commands\ManageCommand();
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
