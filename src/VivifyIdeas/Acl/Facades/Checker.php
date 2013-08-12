<?php

namespace VivifyIdeas\Acl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for Acl\Checker class.
 */
class Checker extends Facade
{

    protected static function getFacadeAccessor() { return 'Acl'; }

}
