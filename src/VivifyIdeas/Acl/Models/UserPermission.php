<?php

namespace VivifyIdeas\Acl\Models;

use Eloquent;

/**
 * Eloquent model for acl_users_permissions table.
 * This is used by Eloquent permissions provider.
 */
class UserPermission extends Eloquent
{
    protected $table = 'acl_users_permissions';

}
