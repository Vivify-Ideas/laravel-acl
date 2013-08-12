<?php

namespace VivifyIdeas\Acl\Commands;

use Illuminate\Console\Command;

/**
 * Custom Artisan command for reseting ACL permissions.
 */
class ManageCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'acl:reset';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reset all ACL permissions. This will delete both user and system permissions and install permissions from config file';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		\AclManager::reloadPermissions();

		$this->info('ACL permissions successful reseted!');
	}

}
