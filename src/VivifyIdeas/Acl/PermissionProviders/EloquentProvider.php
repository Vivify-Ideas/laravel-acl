<?php

namespace VivifyIdeas\Acl\PermissionProviders;

use VivifyIdeas\Acl\Models\UserPermission;
use VivifyIdeas\Acl\Models\Permission;
use VivifyIdeas\Acl\Models\Group;

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
            $permission = $this->parseUserPermission($permission);
        }

        return $userPermissions;
    }

    private function parseUserPermission(array $permission)
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
    public function deleteAllGroups()
    {
        return Group::truncate();
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
                $permission = $this->parseUserPermission($permission);
            }

            return $permissions;
        } else {
            $permission = UserPermission::where('user_id', '=', $userId)
                                ->where('permission_id', '=', $permissionId)
                                ->first();

            if ($permission) {
                return $this->parseUserPermission($permission->toArray());
            }
        }

        return null;
    }

}
