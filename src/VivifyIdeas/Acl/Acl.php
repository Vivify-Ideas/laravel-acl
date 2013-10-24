<?php

namespace VivifyIdeas\Acl;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Main ACL class for checking does user have some permissions.
 */
class Acl
{
    private $manager;
    private $userId = null;
    private $permissionsForChecking = array();

    public function __construct(PermissionsProviderAbstract $provider)
    {
        $this->manager = new Manager($provider);
    }

    /**
     * Setup use for checking privilegues
     *
     * @param integer $userId
     *
     * @return Acl\Checker
     */
    public function user($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Set authenticated or guest user to be current user.
     */
    public function currentUser()
    {
        if (Auth::user()) {
            $this->user(Auth::user()->id);
        } else {
            // guest user
            $this->user(Config::get('acl::guestuser'));
        }
    }

    /**
     * Setup permission that we want to check.
     *
     * We can also specify $resourceId if needed
     * if we want to check some particular resoruce.
     *
     * @param string $permission
     * @param mixed $resourceId
     *
     * @return Acl\Checker
     */
    public function permission($permission, $resourceId = null)
    {
        if (isset($this->permissionsForChecking[$permission])) {
            $this->throwError('Permission "'.$permission.'" is already added for checking.');
        }

        // add permission into array for checking
        $this->permissionsForChecking[$permission] = $resourceId;

        return $this;
    }

    /**
     * Detect is current user superuser.
     *
     * @return boolean
     */
    public function isSuperuser()
    {
        if (!$this->userId) {
            // if user id is not set, try to get authenticated user
            $this->currentUser();
        }

        return ($this->userId !== null &&  in_array($this->userId, Config::get('acl::superusers')));
    }

    /**
     * Return array of superusers IDs
     *
     * @return array
     */
    public function superusers()
    {
        return Config::get('acl::superusers');
    }

    /**
     * Getting current user permissions.
     *
     * @return array
     */
    public function getUserPermissions()
    {
        if (!$this->userId) {
            // if user id is not set, try to get authenticated user
            $this->currentUser();
        }

        return $this->manager->getUserPermissions($this->userId);
    }

    /**
     * Get current user roles (linear structure)
     *
     * @return array
     */
    public function getUserRoles()
    {
        if (!$this->userId) {
            // if user id is not set, try to get authenticated user
            $this->currentUser();
        }

        if ($this->userId) {
            return $this->manager->getUserRoles($this->userId);
        }

        return array();
    }

    /**
     * Check if user have permission to access specifed route.
     *
     * @param string [GET|POST|PUT|DELETE|PATCH] $httpMethod
     * @param string $route
     *
     * @return boolean
     */
    public function checkRoute($httpMethod, $route)
    {
        if ($this->isSuperuser()) {
            return $this->end(true);
        }

        $groups = (array) $this->manager->getGroups();
        $userPermissions = (array) $this->getUserPermissions();

        $list = array_merge($groups, $userPermissions);

        $allowed = true;
        foreach ($list as $item) {
            if (!empty($item['route'])) {
                if (!is_array($item['route'])) {
                    $item['route'] = array($item['route']);
                }

                foreach ($item['route'] as $regExr) {
                    if (($temp = $this->parseRoute($regExr, $route, $httpMethod, $item)) !== null) {
                        $allowed = $allowed && $temp;
                    }
                }
            }
        }

        return $this->end($allowed);
    }

    /**
     * Check permission against route
     *
     * @param string $regExr
     * @param string $route
     * @param string $httpMethod
     * @param array $item Permission or Group array
     *
     * @return boolean|null
     */
    private function parseRoute($regExr, $route, $httpMethod, $item)
    {
        if (preg_match('#' . $regExr . '#', strtoupper($httpMethod) . ':' . $route, $matches)) {
            $resourceId = isset($matches[1])? $matches[1] : null;

            if (isset($item['allowed'])) {
                // this is permission
                return $this->permission($item['id'], $resourceId)->check();
            } else {
                // this is group
                return $this->checkGroup($item['id']);
            }
        }

        return null;
    }

    /**
     * Get resource ids that user can (or not) access.
     *
     * @return array
     */
    public function getResourceIds()
    {
        $ids = array(
            'allowed' => null,
            'allowed_ids' => array(),
            'excluded_ids' => array(),
        );

        if ($this->isSuperuser()) {
            return $this->end( array(
                'allowed' => true,
                'allowed_ids' => array(),
                'excluded_ids' => array(),
            ));
        }

        $userPermissions = $this->getUserPermissions();

        foreach ($this->permissionsForChecking as $permission => $resourceIds) {
            $tempPermission = array(
                'allowed' => $userPermissions[$permission]['allowed'],
                'allowed_ids' => ($userPermissions[$permission]['allowed_ids'] === null)? array() : $userPermissions[$permission]['allowed_ids'],
                'excluded_ids' => ($userPermissions[$permission]['excluded_ids'] === null)? array() : $userPermissions[$permission]['excluded_ids']
            );

            $ids['allowed'] = ($ids['allowed'] === null)? $tempPermission['allowed'] : $ids['allowed'] || $tempPermission['allowed'];
            $ids['allowed_ids'] = array_unique(array_merge($tempPermission['allowed_ids'], $ids['allowed_ids']));
            $ids['excluded_ids'] = array_unique(array_merge($tempPermission['excluded_ids'], $ids['excluded_ids']));
        }

        return $this->end($ids);
    }

    /**
     * Return user ids that can access setup permissions
     *
     * @return array
     */
    public function getUserIds()
    {
        // array of user ids that can access permission
        $ids = array();

        // array of user ids that can not access permission
        $_ids = array();

        // system permissions
        $allPermissions = $this->manager->getAllPermissions();

        foreach ($allPermissions as $key => $permission) {
            unset($allPermissions[$key]);
            $allPermissions[$permission['id']] = $permission;
        }

        foreach ($this->permissionsForChecking as $permissionId => $resourceId) {
            $userPermissions = $this->manager->getUserPermission(null, $permissionId);

            foreach ($userPermissions as $permission) {
                if (@$permission['allowed'] === null) {
                    $permission['allowed'] = $allPermissions[$permission['id']]['allowed'];
                }

                if ($permission['id'] == $permissionId) {
                    if (empty($permission['allowed_ids']) && $permission['allowed']) {
                        $ids[] = $permission['user_id'];
                    }

                    if (empty($permission['allowed_ids']) && $permission['allowed'] == false) {
                        $_ids[] = $permission['user_id'];
                    }

                    if($resourceId !== null) {
                        if (is_array($permission['allowed_ids']) && in_array($resourceId, $permission['allowed_ids'])) {
                            $ids[] = $permission['user_id'];
                        }

                        if (is_array($permission['excluded_ids']) && in_array($resourceId, $permission['excluded_ids'])) {
                            $_ids[] = $permission['user_id'];
                        }
                    }
                }
            }
        }

        $_ids = array_unique($_ids);
        $ids = array_unique(array_merge($ids, $this->superusers()));

        // result formating
        return $this->end(array_merge(array(), array_diff($ids, $_ids)));
    }

    /**
     * Check if user has permission.
     *
     * @return boolean
     */
    public function check()
    {
        if ($this->isSuperuser()) {
            return $this->end(true);
        }

        $allowed = null;
        $userPermissions = $this->getUserPermissions();

        if (empty($userPermissions) || empty($this->permissionsForChecking)) {
            $this->throwError('No permissions for check or no permissions defined.');
        }

        foreach ($this->permissionsForChecking as $permission => $resourceId) {
            $userPermission = @$userPermissions[$permission];

            // check if permission exist in list of all permissions
            if ($userPermission == null) {
                $this->throwError('Permission "'.$permission.'" does not exist.');
            }

            // is resource ID provided for permissions that expect resource ID
            if ($userPermission['resource_id_required'] && empty($resourceId)) {
                $this->throwError('You must specify resource id for permission "'.$permission.'".');
            }

            // if allowed is false break loop and return not allowed
            if ($userPermission['allowed'] === false &&
                empty($userPermission['allowed_ids']) &&
                empty($userPermission['excluded_ids'])
            ) {
                $allowed = false;
                break;
            }

            if ($allowed === null &&
                empty($userPermission['allowed_ids']) &&
                empty($userPermission['excluded_ids'])
            ) {
                // this is first permission for checking
                $allowed = $userPermission['allowed'];
            } else {
                $allowed = true; // TODO: Is this ok solution
            }

            if (empty($userPermission['allowed_ids'])) {
                // there are no specific IDs that are allowed
                $allowed = $allowed && true;
            } else {
                $allowed = $allowed && in_array($resourceId, $userPermission['allowed_ids']);
            }

            if (empty($userPermission['excluded_ids'])) {
                // there are no specific IDs that are excluded
                $allowed = $allowed && true;
            } else {
                $allowed = $allowed && !in_array($resourceId, $userPermission['excluded_ids']);
            }
        }

        return $this->end($allowed);
    }

    /**
     * Check if current user has permission to access specific group.
     *
     * @param string $id
     *
     * @return boolean
     */
    public function checkGroup($id)
    {
        if ($this->isSuperuser()) {
            return $this->end(true);
        }

        $exist = false;

        $ids = array();
        foreach ($this->manager->getChildGroups($id) as $group) {
            $ids[] = $group['id'];
        }

        if (empty($ids)) {
            // route does not exist
            return $this->end(true);
        }

        foreach ($this->getUserPermissions() as $permission) {
            if (in_array(@$permission['group_id'], $ids)) {
                $exist = true;
                if ($permission['allowed'] || !empty($permission['allowed_ids'])) {
                    return $this->end(true);
                }
            }
        }

        return $this->end(!$exist);
    }

    /**
     * Do additonal stuff before returning is permission allowed.
     *
     * @param boolean $return
     *
     * @return boolean
     */
    private function end($return)
    {
        $this->clean();
        return $return;
    }

    /**
     * Clean up then throw and exception.
     *
     * @param string $message
     */
    private function throwError($message) {
        $this->clean();
        throw new \InvalidArgumentException($message);
    }

    /**
     * Clean user and his permissions for checking.
     */
    private function clean()
    {
        $this->permissionsForChecking = array();
        $this->userId = null;
    }

    /**
     * Append to the query additional where statements if needed.
     *
     * @param Illuminate\Database\Eloquent\Builder | Illuminate\Database\Query\Builder $query
     * @param string $primaryKey
     *
     * @return Illuminate\Database\Eloquent\Builder|Illuminate\Database\Query\Builder
     */
    public function buildQuery($query, $primaryKey = 'id')
    {
        // get resource IDs
        $ids = $this->getResourceIds();

        if (empty($ids['allowed_ids']) && empty($ids['excluded_ids'])) {
            if ($ids['allowed']) {
                // alowed all
                return $query;
            } else {
                // not allowed anything
                return false;
            }
        }

        // append excluded ids
        if (!empty($ids['excluded_ids'])) {
            $query->whereNotIn($primaryKey, $ids['excluded_ids']);
        }

        // append allowed ids
        if (!empty($ids['allowed_ids'])) {
            $query->whereIn($primaryKey, $ids['allowed_ids']);
        }

        return $query;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->manager, $name)) {
            return call_user_func_array(array($this->manager, $name), $arguments);
        }

        $this->throwError('Method "'.$name.'" not exist neither in Acl nor in Acl Manager.');
    }

}
