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
    'provider' => 'test',

    /*
    |--------------------------------------------------------------------------
    | Permissions in the application
    |--------------------------------------------------------------------------
    |
    | This option need to contain all system wide permissions.
    |
    | Example:
    | array(
    |     'id' => 'PERMISSION_ID',
    |     'allowed' => true|false,
    |     'route' => array('GET:/resource/(\d+)/edit', 'PUT:/resource/(\d+)'),
    |     'resource_id_required' => true|false,
    |     'name' => 'Permission name'
    | ), array(
    |     'id' => 'PERMISSION_ID_2',
    |     'allowed' => true|false,
    |     'route' => 'GET:/resource/(\d+)',
    |     'resource_id_required' => true|false,
    |     'name' => 'Permission 2 name'
    | ),...
    |
    */
    'permissions' => array(),

    'groups' => array(
        array(
            'id' => 'ADMIN_PRIVILEGES',
            'name' => 'Administrator Privileges',
            'children' => array(
                array(
                    'id' => 'MANAGE_USERS',
                    'name' => 'Manage Users'
                ),
                array(
                    'id' => 'MANAGE_PRODUCTS',
                    'name' => 'Manage Products'
                ),
                array(
                    'id' => 'MANAGE_TAGS',
                    'name' => 'Manage Tags',
                    'children' => array(
                        array(
                            'id' => 'EDIT_TAG',
                            'name' => 'edit tag'
                        )
                    )
                )
            )
        ),
        array(
            'id' => 'STUFF_PRIVILEGES',
            'name' => 'Stuff Privileges',
        )
    )

);
