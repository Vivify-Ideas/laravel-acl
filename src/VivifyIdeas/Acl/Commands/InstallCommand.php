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
			$this->createConfig();

			// remove tables if clean attr exist
			if (Schema::hasTable('acl_permissions')) {
				Schema::drop('acl_permissions');
			}

			if (Schema::hasTable('acl_users_permissions')) {
				Schema::drop('acl_users_permissions');
			}

			if (Schema::hasTable('acl_groups')) {
				Schema::drop('acl_groups');
			}

			if (Schema::hasTable('acl_roles_permissions')) {
			    Schema::drop('acl_roles_permissions');
			}

			if (Schema::hasTable('acl_roles')) {
			    Schema::drop('acl_roles');
			}
			if (Schema::hasTable('acl_users_roles')) {
			    Schema::drop('acl_users_roles');
			}
		} elseif (!file_exists(app_path() . '/config/packages/vivify-ideas/acl/config.php')) {
			$this->createConfig();
		}

		if (Schema::hasTable('acl_permissions') &&
			Schema::hasTable('acl_users_permissions') &&
			Schema::hasTable('acl_groups') &&
			Schema::hasTable('acl_roles')){
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
				$table->string('name');
				$table->string('group_id')->nullable();
			});
		}

		if (!Schema::hasTable('acl_users_permissions')) {
			Schema::create('acl_users_permissions', function(Blueprint $table) {
				$table->increments('id');
				$table->string('permission_id')->index();
				$table->integer('user_id')->index();
				$table->boolean('allowed')->nullable();
				$table->string('allowed_ids')->nullable();
				$table->string('excluded_ids')->nullable();
			});
		}

		if (!Schema::hasTable('acl_groups')) {
			Schema::create('acl_groups', function(Blueprint $table) {
				$table->string('id')->primary();
				$table->string('name');
				$table->string('route')->nullable();
				$table->string('parent_id')->index()->nullable();
			});
		}

		if (!Schema::hasTable('acl_roles_permissions')) {
		    Schema::create('acl_roles_permissions', function(Blueprint $table) {
		        $table->increments('id');
		        $table->string('permission_id')->index();
		        $table->string('role_id')->index();
		        $table->boolean('allowed')->nullable();
		        $table->string('allowed_ids')->nullable();
		        $table->string('excluded_ids')->nullable();
		    });
		}

		if (!Schema::hasTable('acl_roles')) {
		    Schema::create('acl_roles', function(Blueprint $table) {
		        $table->string('id')->primary();
		        $table->string('name');
		        $table->string('parent_id')->index()->nullable();
		    });
		}

		if (!Schema::hasTable('acl_users_roles')) {
		    Schema::create('acl_users_roles', function(Blueprint $table) {
		        $table->increments('id');
		        $table->integer('user_id');
		        $table->string('role_id');
		    });
		}

		$this->info('ACL installed successful!');
	}

	private function createConfig()
	{
		return $this->call('config:publish', array('--path' => 'vendor/vivify-ideas/acl/src/config', 'package' => 'vivify-ideas/acl'));
	}


}
