<?php

namespace VivifyIdeas\Acl;

use Illuminate\Support\Facades\Config;

/**
 * Main ACL class for managing system and user permissions.
 */
class Manager
{
    private $provider;
    private $allPermissions = array();
    private $cached = array();

    public function __construct(PermissionsProviderAbstract $provider)
    {
        $this->provider = $provider;
        
        // set system default permissions
        $this->allPermissions = $this->provider->getAllPermissions();
    }
    
    public function getUserPermissions($userId)
    {
        if (!isset($this->cached[$userId])) {
            // get user permissions
            $userPermissions = $this->provider->getUserPermissions($userId);
            
            $permissions = array();
    
            // get all permissions
            foreach ($this->allPermissions as $permission) {
                $permission['allowed_ids'] = null;
                $permission['excluded_ids'] = null;
                unset($permission['name']);
    
                $permissions[$permission['id']] = $permission;
            }
    
            // overwrite with user permissions
            foreach ($userPermissions as $userPermission) {
                if (@$userPermission['allowed'] === null) {
                    // allowed is not set, so use from system default
                    unset($userPermission['allowed']);
                }
    
                $temp = $permissions[$userPermission['id']];
    
                $temp = array_merge($temp, $userPermission);
    
                $permissions[$userPermission['id']] = $temp;
            }
    
            // set finall permissions for particular user
            $this->cached[$userId] = $permissions;
        }
    
        return $this->cached[$userId];
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
                $permission['name'],
                @$permission['group_id']
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
                $this->insertGroup($group['id'], $group['name'], @$group['route'], $parentGroup);
            } else {
                $newGroups[$group['id']] = $parentGroup;
                $this->insertGroup($group['id'], $group['name'], @$group['route'], $parentGroup);
                $newGroups = array_merge(
                    $newGroups,
                    $this->reloadGroups($group['id'], $group['children'])
                );
            }
        }

        return $newGroups;
    }

    public function insertGroup($id, $name, $route = null, $parentId = null)
    {
        return $this->provider->insertGroup($id, $name, $route, $parentId);
    }

    public function deleteAllGroups()
    {
        return $this->provider->deleteAllGroups();
    }

    public function updateUserPermissions($userId, array $permissions)
    {
        foreach ($permissions as $permission) {
            $this->updateUserPermission(
                $userId,
                $permission['id'],
                @$permission['allowed'],
                @$permission['allowed_ids'],
                @$permission['excluded_ids']
            );
        }
    }

    public function deleteAllPermissions()
    {
        return $this->provider->deleteAllPermissions();
    }

    public function deleteAllUsersPermissions()
    {
        return $this->provider->deleteAllUsersPermissions();
    }

    public function createPermission($id, $allowed, $route, $resourceIdRequired, $name, $groupId = null)
    {
        return $this->provider->createPermission($id, $allowed, $route, $resourceIdRequired, $name, $groupId);
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

    public function getUserPermission($userId, $permissionId)
    {
        return $this->provider->getUserPermission($userId, $permissionId);
    }

    public function setUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        $permission = $this->getUserPermission($userId, $permissionId);
        if (empty($permission)) {
            return $this->provider->assignPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
        } else {
            return $this->provider->updateUserPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
        }
    }

    public function getAllPermissions()
    {
        return $this->provider->getAllPermissions();
    }

    public function getAllPermissionsGrouped($grouped = null, $groups = null)
    {
        $permissions = null;

        if ($grouped === null) {
            $permissions = $this->provider->getAllPermissions();

            $grouped = array();
            foreach ($permissions as $key => $permission) {
                if (isset($permission['group_id'])) {
                    $grouped[$permission['group_id']][] = $permission;
                    unset($permissions[$key]);
                }
            }
        }

        if ($groups === null) {
            $groups = Config::get('acl::groups');
        }

        foreach ($groups as &$group) {
            if (!empty($group['children'])) {
                $temp = $this->getAllPermissionsGrouped($grouped, $group['children']);

                $group['children'] = $temp;

                if (!empty($grouped[$group['id']])) {
                    if (!isset($group['children'])) {
                        $group['children'] = array();
                    }
                    $group['children'] = array_merge($group['children'], $grouped[$group['id']]);
                }
            } else {
                if (!empty($grouped[$group['id']])) {
                    if (!isset($group['children'])) {
                        $group['children'] = array();
                    }
                    $group['children'] = array_merge($group['children'], $grouped[$group['id']]);
                }
            }
        }

        if ($permissions !== null) {
            return array_merge($groups, $permissions);
        }

        return $groups;
    }

    /**
     * List all groups
     */
    public function getGroups()
    {
        return $this->provider->getGroups();
    }
    
    /**
     * List all children of a group
     *
     * @param string|int $id Group ID
     * @param boolean $selfinclude Should we return also the group with provided id
     * @param boolean $recursive Should we return also child of child groups
     * @return array List of children groups
     */
    public function getChildGroups($id, $selfinclude = true, $recursive = true)
    {
        $groups = $this->getGroups();
    
        $childs = array();
        foreach ($groups as $group) {
            if ($group['parent_id'] == $id || ($selfinclude && $group['id'] == $id)) {
                $childs[$group['id']] = $group;
    
                if ($recursive && $group['id'] != $id) {
                    $childs = array_merge($childs, $this->getChildGroups($group['id']));
                }
            }
        }
    
        return $childs;
    }

}
