<?php

namespace VivifyIdeas\Acl\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Database\Schema\Blueprint;
use Schema;

/**
 * Custom Artisan command for installing ACL DB structure.
 */
class InstallCommand extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'acl:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create basic ACL table structure.';

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('clean', InputArgument::OPTIONAL, 'Clean install. Delete "permissions" and "users_permissions" table.'),
		);
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if ($this->argument('clean')) {
			// remove tables if clean attr exist
			if (Schema::hasTable('acl_permissions')) {
				Schema::drop('acl_permissions');
			}

			if (Schema::hasTable('acl_users_permissions')) {
				Schema::drop('acl_users_permissions');
			}
		}

		if (Schema::hasTable('acl_permissions') && Schema::hasTable('acl_users_permissions')) {
			// you already installed ACL
			$this->error('You already installed ACL.');
			return;
		}

		if (!Schema::hasTable('acl_permissions')) {
			Schema::create('acl_permissions', function(Blueprint $table) {
				$table->string('id')->primary();
				$table->boolean('allowed');
				$table->string('route');
				$table->boolean('resource_id_required');
			});
		}

		if (!Schema::hasTable('acl_users_permissions')) {
			Schema::create('acl_users_permissions', function(Blueprint $table) {
				$table->increments('id');
				$table->string('permission_id')->index();
				$table->integer('user_id')->index();
				$table->boolean('allowed')->nullable()->default(null);
				$table->string('allowed_ids')->nullable()->default(null);
				$table->string('excluded_ids')->nullable()->default(null);
			});
		}

		$this->info('ACL installed successful!');
	}

}
