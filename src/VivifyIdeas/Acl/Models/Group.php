<?php

namespace VivifyIdeas\Acl\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
class Group extends Model
{
    protected $table = 'acl_groups';

    protected $fillable = [ 'id', 'name', 'route', 'parent_id' ];

    public $timestamps = false;

    public $incrementing = false;
}
