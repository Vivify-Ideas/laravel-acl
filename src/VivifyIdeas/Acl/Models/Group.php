<?php

namespace VivifyIdeas\Acl\Models;

use Eloquent;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
class Group extends Eloquent
{
    protected $table = 'acl_groups';

    protected $fillable = array('id', 'name', 'route', 'parent_id');

    public $timestamps = false;

}
