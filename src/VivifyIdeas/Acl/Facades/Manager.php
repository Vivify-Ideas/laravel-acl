<?php

namespace VivifyIdeas\Acl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for Acl\Manager class.
 */
class Manager extends Facade
{

    protected static function getFacadeAccessor() { return 'AclManager'; }

}
