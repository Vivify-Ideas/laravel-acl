<?php

namespace VivifyIdeas\Acl\PermissionProviders;

use Illuminate\Support\Facades\Config;

/**
 * Test provider used only for testing purposes.
 * Do not use this provider in real project.
 */
class TestProvider extends \VivifyIdeas\Acl\PermissionsProviderAbstract
{
    public function getUserPermissions($userId)
    {
        if ($userId == 1) {
            return array();
        } else {
            return array(
                array(
                    'id' => 'EDIT_PRODUCT',
                    'allowed' => null,
                    'allowed_ids' => array(2,3,4),
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'VIEW_PRODUCT',
                    'allowed' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'EDIT_USER',
                    'allowed' => null,
                    'allowed_ids' => array(2,3,4),
                    'excluded_ids' => array(9),
                ),
                array(
                    'id' => 'VIEW_USER',
                    'allowed' => null,
                    'allowed_ids' => array(2,3,4),
                    'excluded_ids' => array(9),
                ),
                array(
                    'id' => 'LIST_ASSETS',
                    'allowed' => null,
                    'allowed_ids' => array(2,3,4),
                    'excluded_ids' => null,
                ),
            );
        }
    }

    public function getAllPermissions()
    {
        return Config::get('acl::permissions');
    }

    public function createPermission($id, $allowed, $route, $resourceIdRequired, $name, $groupId = null)
    {
    }

    public function removePermission($id)
    {
    }

    public function assignPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
    }

    public function removeUserPermission($userId, $permissionId)
    {
    }

    public function updateUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
    }

    public function deleteAllPermissions()
    {

    }

    public function deleteAllUsersPermissions()
    {

    }
    
    public function getGroups()
    {
        return array(
            array(
                'id' => 'ADMIN_PRIVILEGES',
                'name' => 'Administrator Privileges',
                'route' => 'GET:/admin.*',
                'parent_id' => null
            ),
            array(
                'id' => 'MANAGE_PRODUCTS',
                'name' => 'Manage Products',
                'route' => 'GET:/admin/products.*',
                'parent_id' => 'ADMIN_PRIVILEGES'
            ),
            array(
                'id' => 'MANAGE_USERS',
                'name' => 'Manage Users',
                'route' => 'GET:/admin/users.*',
                'parent_id' => 'ADMIN_PRIVILEGES'
            ),
        );
    }

    public function insertGroup($id, $name, $parentId = null)
    {

    }

    public function deleteAllGroups()
    {

    }

    public function getUserPermission($userId, $permissionId)
    {

    }

}
