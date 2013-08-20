<?php

/**
 * Test class for Acl\Acl
 *
 * @group acl
 * @group acl.checker
 */
class AclTest extends Orchestra\Testbench\TestCase
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
     * Testing permission method
     */
    public function testPermission()
    {
        $this->assertTrue(Acl::user(2)->permission('LIST_PRODUCTS')->check());

        $this->assertTrue(Acl::user(2)->permission('EDIT_PRODUCT', 2)->check());
        $this->assertFalse(Acl::user(2)->permission('EDIT_PRODUCT', 22)->check());

        $this->assertFalse(Acl::user(2)->permission('VIEW_PRODUCT', 3)->check());

        $this->assertTrue(Acl::user(2)->permission('CREATE_PRODUCT')->check());

        $this->assertFalse(Acl::user(2)->permission('EDIT_USER', 22)->check());
        $this->assertTrue(Acl::user(2)->permission('EDIT_USER', 2)->check());
        $this->assertFalse(Acl::user(2)->permission('EDIT_USER', 9)->check());

        $this->assertTrue(Acl::user(2)->permission('VIEW_USER', 2)->check());
        $this->assertFalse(Acl::user(2)->permission('VIEW_USER', 9)->check());
        $this->assertFalse(Acl::user(2)->permission('VIEW_USER', 99)->check());

        $this->assertTrue(Acl::user(1986)->permission('VIEW_USER', 9)->check());
    }

    /**
     * Testing assinging multiple permissions
     */
    public function testMultipePermission()
    {
        $this->assertTrue(
            Acl::user(2)->permission('LIST_PRODUCTS')
                        ->permission('CREATE_PRODUCT')
                        ->check()
        );

        $this->assertTrue(
            Acl::user(2)->permission('EDIT_USER', 2)
                        ->permission('EDIT_PRODUCT', 2)
                        ->check()
        );

        $this->assertFalse(
            Acl::user(2)->permission('EDIT_PRODUCT', 2)
                        ->permission('VIEW_PRODUCT', 3)
                        ->check()
        );

        $this->assertFalse(
            Acl::user(2)->permission('EDIT_USER', 22)
                        ->permission('VIEW_PRODUCT', 3)
                        ->check()
        );

        $this->assertTrue(
            Acl::user(1986)->permission('EDIT_PRODUCT', 2)
                        ->permission('VIEW_PRODUCT', 3)
                        ->check()
        );
    }

    /**
     * Testing throwing exceptions
     */
    public function testThrowingRightExceptions()
    {
        try {
            Acl::user(2)->permission('EDIT_USER', 2)
                        ->permission('EDIT_USER', 1)
                        ->check();

            $this->fail('Exception not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Permission "EDIT_USER" is already added for checking.', $e->getMessage());
        }

        try {
            Acl::user(3)->check();

            $this->fail('Exception not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('No permissions for check or no permissions defined.', $e->getMessage());
        }

        try {
            Acl::user(2)->permission('NOT_EXISTING_PERMISSION')->check();

            $this->fail('Exception not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Permission "NOT_EXISTING_PERMISSION" does not exist.', $e->getMessage());
        }

        try {
            Acl::user(2)->permission('EDIT_USER')->check();

            $this->fail('Exception not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('You must specify resource id for permission "EDIT_USER".', $e->getMessage());
        }

    }

    /**
     * Testing checkRoute method.
     */
    public function testCheckRoute()
    {
        // route not exist
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/not-found'));

        // LIST_PRODUCTS
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/products'));

        // EDIT_PRODUCT
        $this->assertFalse(Acl::user(2)->checkRoute('GET', '/products/1/edit'));
        $this->assertFalse(Acl::user(2)->checkRoute('PUT', '/products/1'));
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/products/2/edit'));
        $this->assertTrue(Acl::user(2)->checkRoute('PUT', '/products/2'));

        // VIEW_PRODUCT
        $this->assertFalse(Acl::user(2)->checkRoute('GET', '/products/1'));
        $this->assertFalse(Acl::user(2)->checkRoute('GET', '/products/2'));

        // CREATE_PRODUCT
        $this->assertTrue(Acl::user(2)->checkRoute('POST', '/products'));
        $this->assertTrue(Acl::user(2)->checkRoute('POST', '/products/'));
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/products/create'));
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/products/create/'));
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/products/create?filter=true'));

        // EDIT_USER
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/users/2/edit'));
        $this->assertTrue(Acl::user(2)->checkRoute('PUT', '/users/3'));
        $this->assertFalse(Acl::user(2)->checkRoute('PUT', '/users/9'));
        $this->assertFalse(Acl::user(2)->checkRoute('PUT', '/users/99'));

        // VIEW_USER
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/users/2'));
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/users/3'));
        $this->assertFalse(Acl::user(2)->checkRoute('GET', '/users/9'));
        $this->assertFalse(Acl::user(2)->checkRoute('GET', '/users/99'));

        $this->assertTrue(Acl::user(1986)->checkRoute('GET', '/users/99'));

        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/admin'));
        $this->assertTrue(Acl::user(2)->checkRoute('GET', '/admin/products'));
        $this->assertFalse(Acl::user(2)->checkRoute('GET', '/admin/stuff'));
        $this->assertFalse(Acl::user(2)->checkRoute('GET', '/admin/stuff/create'));
    }

    /**
     * Testing getResourceIds method.
     *
     * @group acl.resource-ids
     */
    public function testGetResourceIds()
    {
        $this->assertEquals(
            array(
                'allowed' => true,
                'allowed_ids' => array(2, 3, 4),
                'excluded_ids' => array(9)
            ),
            Acl::user(2)->permission('EDIT_USER')->getResourceIds()
        );

        $this->assertEquals(
            array(
                'allowed' => true,
                'allowed_ids' => array(2,3,4),
                'excluded_ids' => array()
            ),
            Acl::user(2)->permission('EDIT_PRODUCT')->getResourceIds()
        );

        $this->assertEquals(
            array(
                'allowed' => false,
                'allowed_ids' => array(),
                'excluded_ids' => array()
            ),
            Acl::user(2)->permission('VIEW_PRODUCT')->getResourceIds()
        );

        $this->assertEquals(
            array(
                'allowed' => true,
                'allowed_ids' => array(2,3,4),
                'excluded_ids' => array(9)
            ),
            Acl::user(2)->permission('EDIT_PRODUCT')->permission('EDIT_USER')->getResourceIds()
        );

        $this->assertEquals(
            array(
                'allowed' => true,
                'allowed_ids' => array(),
                'excluded_ids' => array()
            ),
            Acl::user(1986)->permission('EDIT_PRODUCT')->permission('EDIT_USER')->getResourceIds()
        );
    }

    /**
     * Testing getUserIds method
     */
    public function testGetUserIds()
    {
        $this->assertEquals(array(4, 1986), Acl::permission('EDIT_PRODUCT')->getUserIds());

        $this->assertEquals(array(1, 3, 4, 6, 1986), Acl::permission('EDIT_PRODUCT', 2)->getUserIds());

        $this->assertEquals(array(1, 4, 1986), Acl::permission('EDIT_PRODUCT', 3)->getUserIds());

        $this->assertEquals(array(1, 4, 1986), Acl::permission('EDIT_PRODUCT', 4)->getUserIds());

        $this->assertEquals(array(2, 3, 4, 1986), Acl::permission('EDIT_PRODUCT', 5)->getUserIds());

        $this->assertEquals(array(1, 1986), Acl::permission('VIEW_PRODUCT', 4)->getUserIds());

        $this->assertEquals(array(1, 3, 4, 1986), Acl::permission('VIEW_PRODUCT', 4)->permission('EDIT_PRODUCT', 5)->getUserIds());

        $this->assertEquals(array(3, 4, 1986), Acl::permission('VIEW_PRODUCT', 44)->permission('EDIT_PRODUCT', 5)->getUserIds());
    }

    /**
     * Testing checkGroup method
     *
     * @group acl.acl.group
     */
    public function testCheckGroup()
    {
        // group does not exist
        $this->assertTrue(Acl::user(2)->checkGroup('NOT_FOUND'));

        // this group does not have any permission assigned
        $this->assertTrue(Acl::user(2)->checkGroup('MANAGE_SPEC_USER'));

        // this group have all allowed permissions
        $this->assertTrue(Acl::user(2)->checkGroup('MANAGE_PRODUCTS'));

        // this group have one not allowed permission
        $this->assertTrue(Acl::user(2)->checkGroup('MANAGE_USERS'));

        // this group have one permission that have allowed_ids set
        $this->assertTrue(Acl::user(2)->checkGroup('MANAGE_STUFF'));

        // this group has one permission that is not allowed
        $this->assertFalse(Acl::user(2)->checkGroup('STUFF_PRIVILEGES'));

        // super user checking
        $this->assertTrue(Acl::user(1986)->checkGroup('STUFF_PRIVILEGES'));

        // parent group permission
        $this->assertFalse(Acl::user(2)->checkGroup('SUPERADMIN_PRIVILEGES'));

    }

    /**
     * Testing getUserPermissions method
     *
     * @dataProvider getPermissions
     */
    public function testGetUserPermissions($userId, $expected)
    {
        $this->assertEquals($expected, Acl::user($userId)->getUserPermissions());
    }

    public function testIsSuperuser()
    {
        $this->assertFalse(Acl::isSuperuser());
        $this->assertFalse(Acl::user(2)->isSuperuser());
        $this->assertTrue(Acl::user(1986)->isSuperuser());

        $this->assertEquals(array(1986), Acl::superusers());
    }

    public function testGetChildGroups()
    {
        $this->assertEquals(5, count(Acl::getChildGroups('ADMIN_PRIVILEGES')));
        $this->assertEquals(4, count(Acl::getChildGroups('ADMIN_PRIVILEGES', false)));
        $this->assertEquals(3, count(Acl::getChildGroups('ADMIN_PRIVILEGES', false, false)));

        $res = Acl::getChildGroups('ADMIN_PRIVILEGES', false);
        $this->assertTrue(isset($res['MANAGE_PRODUCTS']) && isset($res['MANAGE_USERS']) && isset($res['MANAGE_SPEC_USER']));
    }

    /**
     * Data provider for testGetUserPermissions method
     */
    public function getPermissions()
    {
        return array(
            array(1, array(
                'EDIT_PRODUCT' => array(
                    'id' => 'EDIT_PRODUCT',
                    'allowed' => true,
                    'route' => array('GET:/products/(\d+)/edit', 'PUT:/products/(\d+)'),
                    'resource_id_required' => true,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_PRODUCTS'
                ),
                'VIEW_PRODUCT' => array(
                    'id' => 'VIEW_PRODUCT',
                    'allowed' => true,
                    'route' => 'GET:/products/(\d+)$',
                    'resource_id_required' => true,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_PRODUCTS'
                ),
                'CREATE_PRODUCT' => array(
                    'id' => 'CREATE_PRODUCT',
                    'allowed' => true,
                    'route' => array('GET:/products/create', 'POST:/products'),
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_PRODUCTS'
                ),
                'LIST_PRODUCTS' => array(
                    'id' => 'LIST_PRODUCTS',
                    'allowed' => true,
                    'route' => 'GET:/products',
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                ),
                'EDIT_USER' => array(
                    'id' => 'EDIT_USER',
                    'allowed' => true,
                    'route' => array('GET:/users/(\d+)/edit', 'PUT:/users/(\d+)'),
                    'resource_id_required' => true,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_USERS'
                ),
                'VIEW_USER' => array(
                    'id' => 'VIEW_USER',
                    'allowed' => false,
                    'route' => 'GET:/users/(\d+)$',
                    'resource_id_required' => true,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_USERS'
                ),
                'LIST_ASSETS' => array(
                    'id' => 'LIST_ASSETS',
                    'allowed' => false,
                    'route' => 'GET:/assets$',
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_STUFF'
                ),
                'SPEC_USER' => array(
                    'id' => 'SPEC_USER',
                    'allowed' => false,
                    'route' => 'GET:/spec-user$',
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'STUFF_PRIVILEGES'
                ),
                'CREATE_ADMIN' => array(
                    'id' => 'CREATE_ADMIN',
                    'allowed' => false,
                    'route' => 'POST:/admins$',
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_ADMINS'
                ),
            )),

            array(2, array(
                'EDIT_PRODUCT' => array(
                    'id' => 'EDIT_PRODUCT',
                    'allowed' => true,
                    'route' => array('GET:/products/(\d+)/edit', 'PUT:/products/(\d+)'),
                    'resource_id_required' => true,
                    'allowed_ids' => array(2, 3, 4),
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_PRODUCTS'
                ),
                'VIEW_PRODUCT' => array(
                    'id' => 'VIEW_PRODUCT',
                    'allowed' => false,
                    'route' => 'GET:/products/(\d+)$',
                    'resource_id_required' => true,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_PRODUCTS'
                ),
                'CREATE_PRODUCT' => array(
                    'id' => 'CREATE_PRODUCT',
                    'allowed' => true,
                    'route' => array('GET:/products/create', 'POST:/products'),
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_PRODUCTS'
                ),
                'LIST_PRODUCTS' => array(
                    'id' => 'LIST_PRODUCTS',
                    'allowed' => true,
                    'route' => 'GET:/products',
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                ),
                'EDIT_USER' => array(
                    'id' => 'EDIT_USER',
                    'allowed' => true,
                    'route' => array('GET:/users/(\d+)/edit', 'PUT:/users/(\d+)'),
                    'resource_id_required' => true,
                    'allowed_ids' => array(2, 3, 4),
                    'excluded_ids' => array(9),
                    'group_id' => 'MANAGE_USERS'
                ),
                'VIEW_USER' => array(
                    'id' => 'VIEW_USER',
                    'allowed' => false,
                    'route' => 'GET:/users/(\d+)$',
                    'resource_id_required' => true,
                    'allowed_ids' => array(2, 3, 4),
                    'excluded_ids' => array(9),
                    'group_id' => 'MANAGE_USERS'
                ),
                'LIST_ASSETS' => array(
                    'id' => 'LIST_ASSETS',
                    'allowed' => false,
                    'route' => 'GET:/assets$',
                    'resource_id_required' => false,
                    'allowed_ids' => array(2,3,4),
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_STUFF'
                ),
                'SPEC_USER' => array(
                    'id' => 'SPEC_USER',
                    'allowed' => false,
                    'route' => 'GET:/spec-user$',
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'STUFF_PRIVILEGES'
                ),
                'CREATE_ADMIN' => array(
                    'id' => 'CREATE_ADMIN',
                    'allowed' => false,
                    'route' => 'POST:/admins$',
                    'resource_id_required' => false,
                    'allowed_ids' => null,
                    'excluded_ids' => null,
                    'group_id' => 'MANAGE_ADMINS'
                ),
            ))
        );
    }





}
