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
    | This option need to contain all system wide permissions.
    |
    | Example:
    | array(
    |     'id' => 'PERMISSION_ID',
    |     'allowed' => true|false,
    |     'route' => array('GET:/resource/(\d+)/edit', 'PUT:/resource/(\d+)'),
    |     'resource_id_required' => true|false,
    |     'name' => 'Permission name',
    |     'group_id' => 'GROUP_ID_1', // optional
    | ), array(
    |     'id' => 'PERMISSION_ID_2',
    |     'allowed' => true|false,
    |     'route' => 'GET:/resource/(\d+)',
    |     'resource_id_required' => true|false,
    |     'name' => 'Permission 2 name'
    |     'group_id' => 'GROUP_ID_2', // optional
    | ),...
    |
    */
    'permissions' => array(),

    /*
    |--------------------------------------------------------------------------
    | Permission groups
    |--------------------------------------------------------------------------
    |
    | Every permission can belong to some group. You can have groups that
    | belongs to other group.
    |
    | Example:
    | array(
    |     'id' => 'ADMIN_PRIVILEGES',
    |     'name' => 'Administrator Privileges',
    |     'children' => array(
    |         array(
    |             'id' => 'MANAGE_STUFF',
    |             'name' => 'Manage Stuff'
    |         ),
    |         array(
    |             'id' => 'MANAGE_PRODUCTS',
    |             'name' => 'Manage Products'
    |         ),
    |         array(
    |             'id' => 'MANAGE_USERS',
    |             'name' => 'Manage Users',
    |             'children' => array(
    |                 array(
    |                     'id' => 'MANAGE_SPEC_USER',
    |                     'name' => 'Manage spec user'
    |                 )
    |             )
    |         )
    |     )
    | ),
    | array(
    |     'id' => 'STUFF_PRIVILEGES',
    |     'name' => 'Stuff Privileges',
    | )
    |
    */
    'groups' => array(),

);
