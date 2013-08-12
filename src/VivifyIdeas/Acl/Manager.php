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

    public function reloadPermissions($onlySystemPermissions = false)
    {
        $permissions = Config::get('acl::permissions');

        if ($onlySystemPermissions) {
            // delete not existing permissions from users_permissions

            // get old permissions
            $old = $this->provider->getAllPermissions();
            $forDelete = array();
            foreach ($old as $oldPermission) {
                $exist = false;
                foreach ($permissions as $newPermissions) {
                    $exist = $newPermissions['id'] == $oldPermission['id'];

                    if ($exist) {
                        break;
                    }
                }

                if (!$exist) {
                    // delete only user permissions that not exist anymore
                    $forDelete[] = $oldPermission['id'];
                }
            }

            foreach ($forDelete as $id) {
                $this->removeUserPermission(null, $id);
            }
        } else {
            $this->deleteAllUsersPermissions();
        }

        $this->deleteAllPermissions();

        foreach ($permissions as $permission) {
            $this->createPermission(
                $permission['id'],
                $permission['allowed'],
                $permission['route'],
                $permission['resource_id_required']
            );
        }

        return $forDelete;
    }

    public function updateUserPermissions($permissions)
    {
        $this->provider->getUserPermissions();
    }

    public function deleteAllPermissions()
    {
        return $this->provider->deleteAllPermissions();
    }

    public function deleteAllUsersPermissions()
    {
        return $this->provider->deleteAllUsersPermissions();
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

    public function removeUserPermission($userId = null, $permissionId)
    {
        return $this->provider->removeUserPermission($userId, $permissionId);
    }

    public function updateUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return $this->provider->updateUserPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
    }



}
