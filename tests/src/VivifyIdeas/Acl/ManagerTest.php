<?php

/**
 * Test class for Acl\Manager
 *
 * @group acl
 * @group acl.manager
 */
class ManagerTest extends Orchestra\Testbench\TestCase
{

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

    public function testReloadGroups()
    {
        $expected = array(
            'ADMIN_PRIVILEGES' => null,
            'MANAGE_USERS' => 'ADMIN_PRIVILEGES',
            'MANAGE_PRODUCTS' => 'ADMIN_PRIVILEGES',
            'MANAGE_TAGS' => 'ADMIN_PRIVILEGES',
            'EDIT_TAG' => 'MANAGE_TAGS',
            'STUFF_PRIVILEGES' => null
        );

        $this->assertEquals($expected, AclManager::reloadGroups());
    }



}
