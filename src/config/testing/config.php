<?php

return array(

    'provider' => 'test',

    'superusers' => array(1986),

    'guestuser' => 0,

    'userstable' => 'users',

    'permissions' => array(
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
        ),
        array(
            'id' => 'LIST_PRODUCTS',
            'allowed' => true,
            'route' => 'GET:/products',
            'resource_id_required' => false,
            'name' => 'List products',
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
        ),
        array(
            'id' => 'LIST_ASSETS',
            'allowed' => false,
            'route' => 'GET:/assets$',
            'resource_id_required' => false,
            'name' => 'List assets',
            'group_id' => 'MANAGE_STUFF'
        ),
        array(
            'id' => 'SPEC_USER',
            'allowed' => false,
            'route' => 'GET:/spec-user$',
            'resource_id_required' => false,
            'name' => 'Spec user',
            'group_id' => 'STUFF_PRIVILEGES'
        ),
        array(
            'id' => 'CREATE_ADMIN',
            'allowed' => false,
            'route' => 'POST:/admins$',
            'resource_id_required' => false,
            'name' => 'Create admin',
            'group_id' => 'MANAGE_ADMINS'
        ),
    ),

    'groups' => array(
        array(
            'id' => 'ADMIN_PRIVILEGES',
            'name' => 'Administrator Privileges',
            'children' => array(
                array(
                    'id' => 'MANAGE_STUFF',
                    'name' => 'Manage Stuff'
                ),
                array(
                    'id' => 'MANAGE_PRODUCTS',
                    'name' => 'Manage Products'
                ),
                array(
                    'id' => 'MANAGE_USERS',
                    'name' => 'Manage Users',
                    'children' => array(
                        array(
                            'id' => 'MANAGE_SPEC_USER',
                            'name' => 'Manage spec user'
                        )
                    )
                )
            )
        ),
        array(
            'id' => 'STUFF_PRIVILEGES',
            'name' => 'Stuff Privileges',
        ),
        array(
            'id' => 'SUPERADMIN_PRIVILEGES',
            'name' => 'SuperAdmin Privileges',

            'children' => array(
                array(
                    'id' => 'MANAGE_ADMINS',
                    'name' => 'Manage Admins'
                )
            )
        )

    ),

    'roles' => array(
            array(
                    'id' => 'ADMIN',
                    'name' => 'Admin',
                    'permission_ids' => array('ALL'),

                    'children' => array(
                            array(
                                    'id' => 'MODERATOR',
                                    'name' => 'Moderator',
                                    'permission_ids' => array('VIEW_USER', 'EDIT_USER', 'EDIT_USER', 'DELETE_USER'),
                            )
                    )
            )
    ),

);
