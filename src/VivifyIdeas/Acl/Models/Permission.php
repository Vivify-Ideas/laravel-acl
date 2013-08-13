<?php

namespace VivifyIdeas\Acl\Models;

use Eloquent;

/**
 * Eloquent model for acl_permissions table.
 * This is used by Eloquent permissions provider.
 */
class Permission extends Eloquent
{
    protected $table = 'acl_permissions';

    protected $fillable = array('id', 'allowed', 'route', 'resource_id_required', 'name', 'group_id');

    public $timestamps = false;

}
