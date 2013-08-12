<?php

namespace VivifyIdeas\Acl\PermissionProviders;

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
            );
        }
    }

    public function getAllPermissions()
    {
        return array(
            array(
                'id' => 'EDIT_PRODUCT',
                'allowed' => true,
                'route' => array('GET:/products/(\d+)/edit', 'PUT:/products/(\d+)'),
                'resource_id_required' => true
            ),
            array(
                'id' => 'VIEW_PRODUCT',
                'allowed' => true,
                'route' => 'GET:/products/(\d+)$',
                'resource_id_required' => true
            ),
            array(
                'id' => 'CREATE_PRODUCT',
                'allowed' => true,
                'route' => array('GET:/products/create', 'POST:/products'),
                'resource_id_required' => false
            ),
            array(
                'id' => 'LIST_PRODUCTS',
                'allowed' => true,
                'route' => 'GET:/products',
                'resource_id_required' => false
            ),
            array(
                'id' => 'EDIT_USER',
                'allowed' => true,
                'route' => array('GET:/users/(\d+)/edit', 'PUT:/users/(\d+)'),
                'resource_id_required' => true
            ),
            array(
                'id' => 'VIEW_USER',
                'allowed' => false,
                'route' => 'GET:/users/(\d+)$',
                'resource_id_required' => true
            ),
        );
    }

    public function createPermission($id, $allowed, $route, $resourceIdRequired)
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

}
