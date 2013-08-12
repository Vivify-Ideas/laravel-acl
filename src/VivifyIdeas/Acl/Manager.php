<?php

namespace VivifyIdeas\Acl;

use Illuminate\Support\Facades\Config;

/**
 * Main ACL class for managing system and user permissions.
 */
class Manager
{
    private $provider;

    public function __construct(PermissionsProviderAbstract $provider)
    {
        $this->provider = $provider;
    }

    public function reloadPermissions()
    {
        $this->deleteAllPermissions();

        $permissions = Config::get('acl::permissions');

        foreach ($permissions as $permission) {
            $this->createPermission(
                $permission['id'],
                $permission['allowed'],
                $permission['route'],
                $permission['resource_id_required']
            );
        }
    }

    public function deleteAllPermissions()
    {
        return $this->provider->deleteAllPermissions();
    }

    public function createPermission($id, $allowed, $route, $resourceIdRequired)
    {
        return $this->provider->createPermission($id, $allowed, $route, $resourceIdRequired);
    }

    public function removePermission($id)
    {
        return $this->provider->removePermission($id);
    }

    public function assignPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return $this->provider->assignPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
    }

    public function removeUserPermission($userId, $permissionId)
    {
        return $this->provider->removeUserPermission($userId, $permissionId);
    }

    public function updateUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return $this->provider->updateUserPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
    }



}
