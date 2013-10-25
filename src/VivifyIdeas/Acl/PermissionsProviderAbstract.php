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
     * Gets all permissions a role has.
     * Needs to return array of role permissions with following structure:
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
     * @param string $roleId
     *
     * @return array
     */
    public abstract function getRolePermissions($roleId);

    /**
     * Gets all permissions user has based on assigned roles
     * Needs to return array of role permissions with following structure:
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
     * @param string $userId
     *
     * @return array
     */
    public abstract function getUserPermissionsBasedOnRoles($userId);

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

    /**
     * Delete all system wide permissions
     */
    public abstract function deleteAllPermissions();

    /**
     * Delete all user permissions
     */
    public abstract function deleteAllUsersPermissions();

    /**
     * Crate new system permission
     *
     * @param string $id
     * @param bool $allowed
     * @param string|array $route
     * @param bool $resourceIdRequired
     * @param string $name
     *
     * @return array
     */
    public abstract function createPermission($id, $allowed, $route, $resourceIdRequired, $name, $groupId = null);

    /**
     * Remove permission by ID
     *
     * @param string $id
     */
    public abstract function removePermission($id);

    /**
     * Assign permission to the user with specfic options
     *
     * @param integer $userId
     * @param string $permissionId
     * @param boolean $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     */
    public abstract function assignPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    );

    /**
     * Remove specific user permission.
     * If $userId can be null.
     *
     * @param integer $userId
     * @param string $permissionId
     */
    public abstract function removeUserPermission($userId, $permissionId);

    /**
     * Remove all user's permissions.
     *
     * @param integer $userId
     */
    public abstract function removeUserPermissions($userId);

    /**
     * Update specific user permission
     *
     * @param integer $userId
     * @param string $permissionId
     * @param bool $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     */
    public abstract function updateUserPermission(
        $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    );

    /**
     * List all groups (linear structure)
     */
    public abstract function getGroups();

    /**
     * Insert new group
     *
     * @param string $id
     * @param string $name
     * @param array|string $route
     * @param type $parentId
     *
     * @return type
     */
    public abstract function insertGroup($id, $name, $route = null, $parentId = null);

    /**
     * Insert new role
     *
     * @param string $id
     * @param string $name
     * @param array|string $permissionIds
     * @param type $parentId
     *
     * @return type
     */
    public abstract function insertRole($id, $name, $parentId = null);

    /**
     * Delete all groups.
     */
    public abstract function deleteAllGroups();

    /**
     * Delete all roles.
     */
    public abstract function deleteAllRoles();

    /**
     * Get specific user permission
     *
     * @param integer $userId
     * @param string $permissionId
     *
     * @return array
     */
    public abstract function getUserPermission($userId, $permissionId);

    /**
     * Get user roles
     *
     * @param integer $userId
     *
     * @return array
     */
    public abstract function getUserRoles($userId);

}
