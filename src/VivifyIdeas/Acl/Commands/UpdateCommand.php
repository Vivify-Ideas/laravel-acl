<?php

namespace VivifyIdeas\Acl\Commands;

use Illuminate\Console\Command;

/**
 * Custom Artisan command for updating ACL permissions.
 */
class UpdateCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'acl:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update all ACL permissions from config file.';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		\Acl::reloadPermissions(true);

		\Acl::reloadGroups();

		\Acl::reloadRoles();

		$this->info('ACL permissions successful updated!');
	}

}
