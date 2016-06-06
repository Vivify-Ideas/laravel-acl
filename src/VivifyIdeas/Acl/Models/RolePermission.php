<?php

namespace VivifyIdeas\Acl\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for acl_groups table.
 * This is used by Eloquent permissions provider.
 */
class RolePermission extends Model
{
    protected $table = 'acl_roles_permissions';

    protected $fillable = [ 'permission_id', 'role_id', 'allowed', 'allowed_ids', 'excluded_ids' ];

    public $timestamps = false;
}
