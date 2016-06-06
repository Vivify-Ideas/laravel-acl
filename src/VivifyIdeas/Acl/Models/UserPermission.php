<?php

namespace VivifyIdeas\Acl\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for acl_users_permissions table.
 * This is used by Eloquent permissions provider.
 */
class UserPermission extends Model
{
    protected $table = 'acl_users_permissions';

    protected $fillable = [ 'permission_id', 'user_id', 'allowed', 'allowed_ids', 'excluded_ids' ];

    public $timestamps = false;
}
