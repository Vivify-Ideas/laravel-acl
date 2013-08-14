<?php

namespace VivifyIdeas\Acl;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

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

    public function isSuperuser()
    {
        if (!$this->userId) {
            // if user id is not set, try to get authenticated user
            $this->currentUser();
        }

        return ($this->userId !== null &&  in_array($this->userId, Config::get('acl::superusers')));
    }

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
            $this->clean();
            return true;
        }

        $groups = (array) $this->manager->getGroups();
        $userPermissions = (array) $this->getUserPermissions();

        $list = array_merge($groups, $userPermissions);
        foreach ($list as $item) {
            if (is_array($item['route'])) {
                $allowed = null;

                foreach ($item['route'] as $regExr) {
                    if (($temp = $this->parseRoute($regExr, $route, $httpMethod, $item)) !== null) {
                        $allowed = $allowed || $temp;
                    }
                }

                if ($allowed !== null) {
                    $this->clean();
                    return $allowed;
                }
            } else {
                if (!empty($item['route'])) {
                    if (($allowed = $this->parseRoute($item['route'], $route, $httpMethod, $item)) !== null) {
                        $this->clean();
                        return $allowed;
                    }
                }
            }
        }

        $this->clean();
        return true;
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
     * If you pass false as argument, then method will return
     * resource ids that user can not access
     *
     * @param boolean $allowed
     *
     * @return array
     */
    public function getResourceIds($allowed = true)
    {
        $ids = array();

        $userPermissions = $this->getUserPermissions();

        foreach ($this->permissionsForChecking as $permission => $resourceIds) {
            $tempPermission = $userPermissions[$permission];

            $key = ($allowed)? 'allowed_ids' : 'excluded_ids';

            if (!empty($tempPermission[$key])) {
                $ids = array_merge($ids, $tempPermission[$key]);
            }
        }

        $this->clean();
        return array_unique($ids);
    }

    /**
     * Checking does user have setuped permissions.
     *
     * @return boolean
     */
    public function check()
    {
        if ($this->isSuperuser()) {
            $this->clean();
            return true;
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

        $this->clean();

        return $allowed;
    }

    public function checkGroup($id)
    {
        if ($this->isSuperuser()) {
            $this->clean();
            return true;
        }

        $exist = false;

        $ids = array();
        foreach ($this->manager->getChildGroups($id) as $group) {
            $ids[] = $group['id'];
        }

        if (empty($ids)) {
            // route does not exist
            $this->clean();
            return true;
        }

        foreach ($this->getUserPermissions() as $permission) {
            if (in_array(@$permission['group_id'], $ids)) {
                $exist = true;
                if ($permission['allowed'] || !empty($permission['allowed_ids'])) {
                    $this->clean();
                    return true;
                }
            }
        }

        $this->clean();
        return !$exist;
    }

    private function end($return)
    {
        $this->clean();
        return $return;
    }

    /**
     * Throw exception and make additional work.
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
    
    public function __call($name, $arguments)
    {
        if (method_exists($this->manager, $name)) {
            return call_user_func_array(array($this->manager, $name), $arguments);
        }
        
        $this->throwError('Method "'.$name.'" not exist neither in Acl nor in Acl Manager.');
    }

}
