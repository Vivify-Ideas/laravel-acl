<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Permission Provider
    |--------------------------------------------------------------------------
    |
    | This option controls what provider will ACL use.
    | Currently there is only one provider "eloquent".
    |
    | Supported: "eloquent"
    |
    */
    'provider' => 'eloquent',

    /*
    |--------------------------------------------------------------------------
    | Super users array
    |--------------------------------------------------------------------------
    |
    | Put here user IDs that will have superuser rights.
    |
    */
    'superusers' => array(),

    /*
    |--------------------------------------------------------------------------
    | Guest users ID
    |--------------------------------------------------------------------------
    |
    | Put here ID that will used for setting permissions to guest users.
    |
    */
    'guestuser' => 0,

    /*
    |--------------------------------------------------------------------------
    | Permissions in the application
    |--------------------------------------------------------------------------
    |
    | This option needs to contain all system wide permissions.
    |
    | Example:

        'permissions' => array(
            // users
            array(
                'id' => 'CREATE_USER',
                'name' => 'Create User',
                'allowed' => false,
                'route' => array('GET:/admin/users/create', 'POST:/admin/users'),
                'resource_id_required' => false,
                'group_id' => 'MANAGE_USERS'
            ),
            array(
                'id' => 'EDIT_USER',
                'name' => 'Edit User',
                'allowed' => false,
                'route' => array(
                    'GET:/admin/users/(\d+)/edit',
                    'PUT:/admin/users/(\d+)',
                    'GET:/admin/users/(\d+)/permissions',
                    'PUT:/admin/users/(\d+)/permissions',
                    'PUT:/admin/users/(\d+)/member-type'
                ),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_USERS'
            ),
            array(
                'id' => 'DELETE_USER',
                'name' => 'Delete User',
                'allowed' => false,
                'route' => array('DELETE:/admin/users/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_USERS'
            ),

            // products
            array(
                'id' => 'CREATE_PRODUCT',
                'name' => 'Create Product',
                'allowed' => false,
                'route' => array('GET:/admin/products/create', 'POST:/admin/products$'),
                'resource_id_required' => false,
                'group_id' => 'MANAGE_PRODUCTS'
            ),
            array(
                'id' => 'EDIT_PRODUCT',
                'name' => 'Edit Product',
                'allowed' => false,
                'route' => array('GET:/admin/products/(\d+)/edit', 'PUT:/admin/products/(\d+)', 'GET:/admin/products/(\d+)/speakers'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_PRODUCTS'
            ),
            array(
                'id' => 'DELETE_PRODUCT',
                'name' => 'Delete Product',
                'allowed' => false,
                'route' => array('DELETE:/admin/products/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_PRODUCTS'
            ),

            // assets
            array(
                'id' => 'CREATE_ASSET',
                'name' => 'Create Asset',
                'allowed' => false,
                'route' => array('GET:/admin/products/(\d+)/assets/create', 'POST:/admin/products/(\d+)/assets$'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_ASSETS'
            ),
            array(
                'id' => 'EDIT_ASSET',
                'name' => 'Edit Asset',
                'allowed' => false,
                'route' => array('GET:/admin/products/\d+/assets/(\d+)/edit', 'PATCH:/admin/products/\d+/assets/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_ASSETS'
            ),
            array(
                'id' => 'DELETE_ASSET',
                'name' => 'Delete Asset',
                'allowed' => false,
                'route' => array('DELETE:/admin/products/\d+/assets/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_ASSETS'
            ),

            // categories
            array(
                'id' => 'CREATE_CATEGORY',
                'name' => 'Create Category',
                'allowed' => false,
                'route' => array('GET:/admin/categories/create', 'POST:/admin/categories'),
                'resource_id_required' => false,
                'group_id' => 'MANAGE_CATEGORIES'
            ),
            array(
                'id' => 'EDIT_CATEGORY',
                'name' => 'Edit Category',
                'allowed' => false,
                'route' => array('GET:/admin/categories/(\d+)/edit', 'PUT:/admin/categories/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_CATEGORIES'
            ),
            array(
                'id' => 'DELETE_CATEGORY',
                'name' => 'Delete Category',
                'allowed' => false,
                'route' => array('DELETE:/admin/categories/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_CATEGORIES'
            ),

            // bundles
            array(
                'id' => 'CREATE_BUNDLE',
                'name' => 'Create Bundle',
                'allowed' => false,
                'route' => array('GET:/admin/bundles/create', 'POST:/admin/bundles'),
                'resource_id_required' => false,
                'group_id' => 'MANAGE_BUNDLES'
            ),
            array(
                'id' => 'EDIT_BUNDLE',
                'name' => 'Edit Bundle',
                'allowed' => false,
                'route' => array('GET:/admin/bundles/(\d+)/edit', 'PUT:/admin/bundles/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_BUNDLES'
            ),
            array(
                'id' => 'DELETE_BUNDLE',
                'name' => 'Delete Bundle',
                'allowed' => false,
                'route' => array('DELETE:/admin/bundles/(\d+)'),
                'resource_id_required' => true,
                'group_id' => 'MANAGE_BUNDLES'
            ),

            // Account Settings
            array(
                'id' => 'EDIT_ACCOUNT_SETTINGS',
                'name' => 'Edit Account Settings',
                'allowed' => false,
                'route' => '.*:/admin/settings',
                'resource_id_required' => false,
                'group_id' => 'ADMIN_PRIVILEGES'
            ),

            // Newsletters
            array(
                'id' => 'MANAGE_NEWSLETTERS',
                'name' => 'Manage Newsletters',
                'allowed' => false,
                'route' => '.*:/admin/newsletters',
                'resource_id_required' => false,
                'group_id' => 'ADMIN_PRIVILEGES'
            ),
        ),
    */
    'permissions' => array(),

    /*
    |--------------------------------------------------------------------------
    | Permission groups
    |--------------------------------------------------------------------------
    |
    | Every permission can belong to some group. You can have groups that
    | belongs to other group. Every group can have a route.
    |
    | Example:
        'groups' => array(
            array(
                'id' => 'ADMIN_PRIVILEGES',
                'name' => 'Administrative Privileges',
                'route' => 'GET:/admin',

                'children' => array(
                    array(
                        'id' => 'MANAGE_USERS',
                        'name' => 'Manage Users',
                        'route' => 'GET:/admin/users'
                    ),
                    array(
                        'id' => 'MANAGE_PRODUCTS',
                        'name' => 'Manage Products',
                        'route' => 'GET:/admin/products',
                        'children' => array(
                            array(
                                'id' => 'MANAGE_ASSETS',
                                'name' => 'Manage Assets',
                                'route' => 'GET:/admin/products/\d+/assets'
                            ),
                        )
                    ),
                    array(
                        'id' => 'MANAGE_CATEGORIES',
                        'name' => 'Manage Categories',
                        'route' => 'GET:/admin/categories'
                    ),
                    array(
                        'id' => 'MANAGE_BUNDLES',
                        'name' => 'Manage Bundles',
                        'route' => 'GET:/admin/bundles'
                    )
                )
            )
        ),
    */
    'groups' => array(),

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | Roles can have set of permissions as well as parent and children roles.
    | To use roles add roles column to your users table.
    |
    |
    | Example:
       'roles' => array(
            array(
                'id' => 'ADMIN',
                'name' => 'Admin',
            ),
            array(
                'id' => 'MODERATOR',
                'name' => 'Moderator',
            )
       ),
   */
    'roles' => array(),

);

