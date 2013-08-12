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

    protected $fillable = array('permission_id', 'user_id', 'allowed', 'allowed_ids', 'excluded_ids');

    public $timestamps = false;


}
