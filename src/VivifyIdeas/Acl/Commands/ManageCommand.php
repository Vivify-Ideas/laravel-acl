<?php

namespace VivifyIdeas\Acl\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Database\Schema\Blueprint;
use Schema;

use VivifyIdeas\Acl\Manager;
use VivifyIdeas\Acl\PermissionProviders\EloquentProvider;

/**
 * Custom Artisan command for installing ACL DB structure.
 */
class ManageCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'acl:manage';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Manage ACL permissions.';

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('reload-permissions', InputArgument::REQUIRED, 'Read permssions from config file and insert then using current permission provider.'),
		);
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$manager = new Manager(new EloquentProvider);

		die(var_dump($manager->reloadPermissions()));

		$this->info('ACL permissions successful inserted!');
	}

}
