<?php

namespace VivifyIdeas\Acl\Models;

use Eloquent;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
class RolePermission extends Eloquent
{
    protected $table = 'acl_roles_permissions';

    protected $fillable = array('permission_id', 'role_id', 'allowed', 'allowed_ids', 'excluded_ids');

    public $timestamps = false;

}
