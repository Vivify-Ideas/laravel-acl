<?php

/**
 * Test class for Acl\Manager
 *
 * @group acl
 * @group acl.manager
 */
class ManagerTest extends Orchestra\Testbench\TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->app->bind('AclManager', function() {
            return new VivifyIdeas\Acl\Manager(new VivifyIdeas\Acl\PermissionProviders\TestProvider);
        });
    }

    protected function getPackageProviders()
    {
        return array('VivifyIdeas\Acl\AclServiceProvider');
    }

    protected function getPackageAliases()
    {
        return array(
            'AclManager' => 'VivifyIdeas\Acl\Facades\Manager',
        );
    }

    /**
     * Testing reloadPermissions method
     */
    public function testReloadPermissions()
    {
        $expected = array(
            "EDIT_PRODUCT",
            "VIEW_PRODUCT",
            "CREATE_PRODUCT",
            "LIST_PRODUCTS",
            "EDIT_USER",
            "VIEW_USER"
        );

        $this->assertEquals($expected, AclManager::reloadPermissions(true));

    }



}
