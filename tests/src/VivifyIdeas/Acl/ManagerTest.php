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
            'Acl' => 'VivifyIdeas\Acl\Facades\Acl',
        );
    }

    /**
     * Testing reloadPermissions method
     */
    public function testReloadPermissions()
    {
        $this->assertEquals(array(), Acl::reloadPermissions(true));
    }

    public function testReloadGroups()
    {
        $expected = array(
            'ADMIN_PRIVILEGES' => null,
            'MANAGE_STUFF' => 'ADMIN_PRIVILEGES',
            'MANAGE_PRODUCTS' => 'ADMIN_PRIVILEGES',
            'MANAGE_USERS' => 'ADMIN_PRIVILEGES',
            'MANAGE_SPEC_USER' => 'MANAGE_USERS',
            'STUFF_PRIVILEGES' => null,
            'SUPERADMIN_PRIVILEGES' => null,
            'MANAGE_ADMINS' => 'SUPERADMIN_PRIVILEGES'

        );

        $this->assertEquals($expected, Acl::reloadGroups());
    }

    public function testGetAllPermissionsGrouped()
    {
        $expected = array(
            array(
                'id' => 'ADMIN_PRIVILEGES',
                'name' => 'Administrator Privileges',
                'children' => array(
                    array(
                        'id' => 'MANAGE_STUFF',
                        'name' => 'Manage Stuff',
                        'children' => array(
                            array(
                                'id' => 'LIST_ASSETS',
                                'allowed' => false,
                                'route' => 'GET:/assets$',
                                'resource_id_required' => false,
                                'name' => 'List assets',
                                'group_id' => 'MANAGE_STUFF'
                            ),
                        )
                    ),
                    array(
                        'id' => 'MANAGE_PRODUCTS',
                        'name' => 'Manage Products',
                        'children' => array(
                            array(
                                'id' => 'EDIT_PRODUCT',
                                'allowed' => true,
                                'route' => array('GET:/products/(\d+)/edit', 'PUT:/products/(\d+)'),
                                'resource_id_required' => true,
                                'name' => 'Edit product',
                                'group_id' => 'MANAGE_PRODUCTS'
                            ),
                            array(
                                'id' => 'VIEW_PRODUCT',
                                'allowed' => true,
                                'route' => 'GET:/products/(\d+)$',
                                'resource_id_required' => true,
                                'name' => 'View product',
                                'group_id' => 'MANAGE_PRODUCTS'
                            ),
                            array(
                                'id' => 'CREATE_PRODUCT',
                                'allowed' => true,
                                'route' => array('GET:/products/create', 'POST:/products'),
                                'resource_id_required' => false,
                                'name' => 'Create product',
                                'group_id' => 'MANAGE_PRODUCTS'
                            )
                        )
                    ),
                    array(
                        'id' => 'MANAGE_USERS',
                        'name' => 'Manage Users',
                        'children' => array(
                            array(
                                'id' => 'MANAGE_SPEC_USER',
                                'name' => 'Manage spec user',
                            ),
                            array(
                                'id' => 'EDIT_USER',
                                'allowed' => true,
                                'route' => array('GET:/users/(\d+)/edit', 'PUT:/users/(\d+)'),
                                'resource_id_required' => true,
                                'name' => 'Edit user',
                                'group_id' => 'MANAGE_USERS'
                            ),
                            array(
                                'id' => 'VIEW_USER',
                                'allowed' => false,
                                'route' => 'GET:/users/(\d+)$',
                                'resource_id_required' => true,
                                'name' => 'View user',
                                'group_id' => 'MANAGE_USERS'
                            )
                        )
                    )
                )
            ),
            array(
                'id' => 'STUFF_PRIVILEGES',
                'name' => 'Stuff Privileges',
                'children' => array(
                    array(
                        'id' => 'SPEC_USER',
                        'allowed' => false,
                        'route' => 'GET:/spec-user$',
                        'resource_id_required' => false,
                        'name' => 'Spec user',
                        'group_id' => 'STUFF_PRIVILEGES'
                    )
                )
            ),
            array(
                'id' => 'SUPERADMIN_PRIVILEGES',
                'name' => 'SuperAdmin Privileges',

                'children' => array(
                    array(
                        'id' => 'MANAGE_ADMINS',
                        'name' => 'Manage Admins',
                        'children' => array(
                            array(
                                'id' => 'CREATE_ADMIN',
                                'allowed' => false,
                                'route' => 'POST:/admins$',
                                'resource_id_required' => false,
                                'name' => 'Create admin',
                                'group_id' => 'MANAGE_ADMINS'
                            ),
                        )
                    )
                )
            ),
            array(
                'id' => 'LIST_PRODUCTS',
                'allowed' => true,
                'route' => 'GET:/products',
                'resource_id_required' => false,
                'name' => 'List products',
            )
        );

        $actual = Acl::getAllPermissionsGrouped();

        $this->assertEquals($expected, Acl::getAllPermissionsGrouped());
    }


}
