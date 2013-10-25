<?php

namespace VivifyIdeas\Acl\PermissionProviders;

use VivifyIdeas\Acl\Models\UserPermission;
use VivifyIdeas\Acl\Models\Permission;
use VivifyIdeas\Acl\Models\Group;
use VivifyIdeas\Acl\Models\Role;
use VivifyIdeas\Acl\Models\RolePermission;
use VivifyIdeas\Acl\Models\UserRole;

/**
 * Default Eloquent permission provider.
 */
class EloquentProvider extends \VivifyIdeas\Acl\PermissionsProviderAbstract
{
    /**
     * @see parent description
     */
    public function getUserPermissions($userId)
    {
        $userPermissions = UserPermission::where('user_id', '=', $userId)->get()->toArray();

        foreach ($userPermissions as &$permission) {
            $permission = $this->parseUserOrRolePermission($permission);
        }

        return $userPermissions;
    }

    /**
     * @see parent description
     */
    public function getRolePermissions($roleId)
    {
        $rolePermissions = RolePermission::where('role_id', '=', $roleId)->get()->toArray();

        foreach ($rolePermissions as &$permission) {
            $permission = $this->parseUserOrRolePermission($permission);
        }

        return $rolePermissions;
    }

    /**
     * @see parent description
     */
    public function getUserPermissionsBasedOnRoles($userId)
    {
        $userRolePermissions = UserRole::where('user_id', $userId)
            ->leftJoin('acl_roles_permissions', 'acl_users_roles.role_id', '=', 'acl_roles_permissions.role_id')
            ->get(array('acl_roles_permissions.*'))->toArray();

        foreach ($userRolePermissions as &$permission) {
            $permission = $this->parseUserOrRolePermission($permission);
        }

        return $userRolePermissions;
    }

    private function parseUserOrRolePermission(array $permission)
    {
        if (empty($permission)) {
            return $permission;
        }

        if ($permission['allowed'] === null) {
            // allowed is not set, so use from system default
            unset($permission['allowed']);
        } else {
            $permission['allowed'] = (bool) $permission['allowed'];
        }

        $permission['id'] = $permission['permission_id'];
        unset($permission['permission_id']);

        if ($permission['allowed_ids'] != null) {
            // create array from string - try to explode by ','
            $permission['allowed_ids'] = explode(',', $permission['allowed_ids']);
        }

        if ($permission['excluded_ids'] != null) {
            // create array from string - try to explode by ','
            $permission['excluded_ids'] = explode(',', $permission['excluded_ids']);
        }

        return $permission;
    }

    /**
     * @see parent description
     */
    public function getAllPermissions()
    {
        $permissions = Permission::all()->toArray();

        foreach ($permissions as &$permission) {
            $routes = json_decode($permission['route'], true);
            if ($routes !== null) {
                // if route is json encoded string
                $permission['route'] = $routes;
            }

            $permission['allowed'] = (bool) $permission['allowed'];
            $permission['resource_id_required'] = (bool) $permission['resource_id_required'];
        }

        return $permissions;
    }

    /**
     * @see parent description
     */
    public function createPermission($id, $allowed, $route, $resourceIdRequired, $name, $groupId = null)
    {
        return Permission::create(array(
            'id' => $id,
            'allowed' => $allowed,
            'route' => is_array($route)? json_encode($route) : $route,
            'resource_id_required' => $resourceIdRequired,
            'name' => $name,
            'group_id' => $groupId
        ))->toArray();
    }

    /**
     * @see parent description
     */
    public function removePermission($id)
    {
        return Permission::destroy($id);
    }

    /**
     * @see parent description
     */
    public function assignPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return UserPermission::create(array(
            'permission_id' => $permissionId,
            'user_id' => $userId,
            'allowed' => $allowed,
            'allowed_ids' => (!empty($allowedIds))? implode(',', $allowedIds) : null,
            'excluded_ids' => (!empty($excludedIds))? implode(',', $excludedIds) : null,
        ))->toArray();
    }

    /**
     * @see parent description
     */
    public function removeUserPermission($userId, $permissionId)
    {
        $q = UserPermission::where('permission_id', '=', $permissionId);

        if ($userId !== null) {
            $q->where('user_id', '=', $userId);
        }

        return $q->delete();
    }

    /**
     * @see parent description
     */
    public function removeUserPermissions($userId)
    {
        return UserPermission::where('user_id', '=', $userId)->delete();
    }

    /**
     * @see parent description
     */
    public function updateUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return UserPermission::where('user_id', '=', $userId)
                            ->where('permission_id', '=', $permissionId)
                            ->update(array(
                                'allowed' => $allowed,
                                'allowed_ids' => (!empty($allowedIds))? implode(',', $allowedIds) : null,
                                'excluded_ids' => (!empty($excludedIds))? implode(',', $excludedIds) : null,
                            ));
    }

    /**
     * @see parent description
     */
    public function deleteAllPermissions()
    {
        return Permission::truncate();
    }

    /**
     * @see parent description
     */
    public function deleteAllUsersPermissions()
    {
        return UserPermission::truncate();
    }

    /**
     * @see parent description
     */
    public function getGroups()
    {
        $groups = Group::all()->toArray();

        foreach ($groups as &$group) {
            $routes = json_decode($group['route'], true);
            if ($routes !== null) {
                // if route is json encoded string
                $group['route'] = $routes;
            }
        }

        return $groups;
    }

    /**
     * @see parent description
     */
    public function insertGroup($id, $name, $route = null, $parentId = null)
    {
        return Group::create(array(
            'id' => $id,
            'name' => $name,
            'route' => is_array($route)? json_encode($route) : $route,
            'parent_id' => $parentId
        ))->toArray();
    }

    /**
     * @see parent description
     */
    public function insertRole($id, $name, $parentId = null)
    {
        return Role::create(array(
                'id' => $id,
                'name' => $name,
                'parent_id' => $parentId
        ))->toArray();
    }

    /**
     * @see parent description
     */
    public function deleteAllGroups()
    {
        return Group::truncate();
    }

    /**
     * @see parent description
     */
    public function deleteAllRoles()
    {
        return Role::truncate();
    }

    /**
     * @see parent description
     */
    public function getUserPermission($userId, $permissionId)
    {
        if ($userId === null) {
            // if user is not specified then return all user permissions with specific permission_id
            $permissions = UserPermission::where('permission_id', '=', $permissionId)->get()->toArray();
            foreach ($permissions as &$permission) {
                $permission = $this->parseUserOrRolePermission($permission);
            }

            return $permissions;
        } else {
            $permission = UserPermission::where('user_id', '=', $userId)
                                ->where('permission_id', '=', $permissionId)
                                ->first();

            if ($permission) {
                return $this->parseUserOrRolePermission($permission->toArray());
            }
        }

        return null;
    }

    /**
     * @see parent description
     */
    public function getUserRoles($userId)
    {
        return UserRole::where('user_id', $userId)->lists('role_id');
    }

}
