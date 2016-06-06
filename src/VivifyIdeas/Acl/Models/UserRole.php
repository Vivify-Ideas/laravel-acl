<?php

namespace VivifyIdeas\Acl\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
class UserRole extends Model
{
    protected $table = 'acl_users_roles';

    protected $fillable = [ 'user_id', 'role_id' ];

    public $timestamps = false;
}
