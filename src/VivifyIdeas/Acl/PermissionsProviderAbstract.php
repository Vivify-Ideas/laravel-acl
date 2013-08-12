<?php

namespace VivifyIdeas\Acl;

/**
 * Abstract class for getting permissions.
 *
 * Acl\Checker works with this class so it can retrieve
 * global and users permissions.
 */
abstract class PermissionsProviderAbstract
{

    /**
     * Needs to return array of user permissions with following structure:
     *
     * array(
     *  array(
     *      'id' => 'PERMISSION_ID',
     *      'allowed' => null|true|false,
     *      'allowed_ids' => null|2|array(1,2,3),
     *      'excluded_ids' => null|2|array(1,2,3)
     *  ),...
     * )
     *
     * @param integer $userId
     *
     * @return array
     */
    public abstract function getUserPermissions($userId);

    /**
     * Needs to return array of all system permissions with following structure:
     *
     * array(
     *  array(
     *      'id' => 'PERMISSION_ID',
     *      'allowed' => true|false,
     *      'route' => 'GET:/resource$'|array('GET:/resource$','POST:/resource$'),
     *      'resource_id_required' => true|false
     *  ),...
     * )
     *
     * @return array
     */
    public abstract function getAllPermissions();

    public abstract function deleteAllPermissions();

    public abstract function deleteAllUsersPermissions();

    public abstract function createPermission($id, $allowed, $route, $resourceIdRequired);

    public abstract function removePermission($id);

    public abstract function assignPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    );

    public abstract function removeUserPermission($userId, $permissionId);

    public abstract function updateUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    );

}
