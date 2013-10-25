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

    public function getRolePermissions($roleId)
    {
        if ($roleId == 1) {
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

    public function getUserPermissionsBasedOnRoles($userId)
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

    public function removeUserPermissions($userId)
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
            array(
                'id' => 'MANAGE_STUFF',
                'name' => 'Manage Stuff',
                'route' => null,
                'parent_id' => 'ADMIN_PRIVILEGES'
            ),
            array(
                'id' => 'MANAGE_SPEC_USER',
                'name' => 'Manage Spec user',
                'route' => null,
                'parent_id' => 'MANAGE_USERS'
            ),
            array(
                'id' => 'STUFF_PRIVILEGES',
                'name' => 'Stuff Privileges',
                'route' => 'GET:/admin/stuff',
                'parent_id' => null
            ),
            array(
                'id' => 'SUPERADMIN_PRIVILEGES',
                'name' => 'Administrator Super Privileges',
                'route' => null,
                'parent_id' => null
            ),
            array(
                'id' => 'MANAGE_ADMINS',
                'name' => 'Manage Stuff',
                'route' => null,
                'parent_id' => 'SUPERADMIN_PRIVILEGES'
            ),
        );
    }

    public function getUserRoles($userId)
    {
        return explode(',', 'EDITOR,TRANSLATOR');
    }

    public function insertGroup($id, $name, $route = null, $parentId = null)
    {

    }

    public function insertRole($id, $name, $parentId = null)
    {

    }

    public function deleteAllGroups()
    {

    }

    public function deleteAllRoles()
    {

    }

    public function getUserPermission($userId, $permissionId)
    {
        if ($userId === null) {
            return array(
                array(
                    'id' => 'EDIT_PRODUCT',
                    'user_id' => 1,
                    'allowed' => null,
                    'allowed_ids' => array(2,3,4),
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'EDIT_PRODUCT',
                    'user_id' => 2,
                    'allowed' => null,
                    'allowed_ids' => array(5),
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'EDIT_PRODUCT',
                    'user_id' => 3,
                    'allowed' => null,
                    'allowed_ids' => array(2,5),
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'EDIT_PRODUCT',
                    'user_id' => 4,
                    'allowed' => null,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'EDIT_PRODUCT',
                    'user_id' => 5,
                    'allowed' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'EDIT_PRODUCT',
                    'user_id' => 6,
                    'allowed' => false,
                    'allowed_ids' => array(2),
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'VIEW_PRODUCT',
                    'user_id' => 1,
                    'allowed' => false,
                    'allowed_ids' => array(4),
                    'excluded_ids' => null,
                ),
                array(
                    'id' => 'VIEW_PRODUCT',
                    'user_id' => 2,
                    'allowed' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                ),
            );
        }
    }

}
