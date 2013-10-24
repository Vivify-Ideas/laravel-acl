<?php

namespace VivifyIdeas\Acl\Models;

use Eloquent;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
class Role extends Eloquent
{
    protected $table = 'acl_roles';

    protected $fillable = array('id', 'name', 'permission_ids', 'parent_id');

    public $timestamps = false;

}
