<?php

namespace VivifyIdeas\Acl\PermissionProviders;

use VivifyIdeas\Acl\Models\UserPermission;
use VivifyIdeas\Acl\Models\Permission;

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
            if ($permission['allowed'] === null) {
                // allowed is not set, so use from system default
                unset($permission['allowed']);
            }

            if ($permission['allowed_ids'] !== null) {
                // create array from string - try to explode by ','
                $permission['allowed_ids'] = explode(',', $permission['allowed_ids']);
            }

            if ($permission['excluded_ids'] !== null) {
                // create array from string - try to explode by ','
                $permission['excluded_ids'] = explode(',', $permission['excluded_ids']);
            }
        }

        return $userPermissions;
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
        }

        return $permissions;
    }

    public function createPermission($id, $allowed, $route, $resourceIdRequired)
    {
        return Permission::create(array(
            'id' => $id,
            'allowed' => $allowed,
            'route' => is_array($route)? json_encode($route) : $route,
            'resource_id_required' => $resourceIdRequired
        ))->toArray();
    }

    public function removePermission($id)
    {
        return Permission::destroy($id);
    }

    public function assignPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return UserPermission::create(array(
            'id' => $permissionId,
            'user_id' => $userId,
            'allowed' => $allowed,
            'allowed_ids' => ($allowedIds !== null)? implode(',', $allowedIds) : $allowedIds,
            'excluded_ids' => ($excludedIds !== null)? implode(',', $excludedIds) : $excludedIds,
        ))->toArray();
    }

    public function removeUserPermission($userId, $permissionId)
    {
        return UserPermission::where('user_id', '=', $userId)
                            ->where('id', '=', $permissionId)
                            ->delete();
    }

    public function updateUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return UserPermission::where('user_id', '=', $userId)
                            ->where('id', '=', $permissionId)
                            ->update(array(
                                'allowed' => $allowed,
                                'allowed_ids' => ($allowedIds !== null)? implode(',', $allowedIds) : $allowedIds,
                                'excluded_ids' => ($excludedIds !== null)? implode(',', $excludedIds) : $excludedIds,
                            ));
    }

    public function deleteAllPermissions()
    {
        return Permission::truncate();
    }

}
