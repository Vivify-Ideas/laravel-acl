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
        $forDelete = array();

        if ($onlySystemPermissions) {
            // delete not existing permissions from users_permissions

            // get old permissions
            $old = $this->provider->getAllPermissions();
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
                $permission['resource_id_required'],
                $permission['name']
            );
        }

        return $forDelete;
    }

    public function reloadGroups($parentGroup = null, $groups = null)
    {
        if (empty($groups)) {
            $groups = Config::get('acl::groups');
        }

        if ($parentGroup === null) {
            $this->deleteAllGroups();
        }

        $newGroups = array();

        foreach ($groups as $group) {
            if (empty($group['children'])) {
                $newGroups[$group['id']] = $parentGroup;
                $this->insertGroup($group['id'], $group['name'], $parentGroup);
            } else {
                $newGroups[$group['id']] = $parentGroup;
                $this->insertGroup($group['id'], $group['name'], $parentGroup);
                $newGroups = array_merge(
                    $newGroups,
                    $this->reloadGroups($group['id'], $group['children'])
                );
            }
        }

        return $newGroups;
    }

    public function insertGroup($id, $name, $parentId = null)
    {
        return $this->provider->insertGroup($id, $name, $parentId);
    }

    public function deleteAllGroups()
    {
        return $this->provider->deleteAllGroups();
    }

    public function updateUserPermissions($permissions)
    {
        return $this->provider->getUserPermissions();
    }

    public function deleteAllPermissions()
    {
        return $this->provider->deleteAllPermissions();
    }

    public function deleteAllUsersPermissions()
    {
        return $this->provider->deleteAllUsersPermissions();
    }

    public function createPermission($id, $allowed, $route, $resourceIdRequired, $name)
    {
        return $this->provider->createPermission($id, $allowed, $route, $resourceIdRequired, $name);
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

    public function getAllPermissions()
    {
        return $this->provider->getAllPermissions();
    }



}
