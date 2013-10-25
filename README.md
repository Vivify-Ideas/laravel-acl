Laravel ACL
===========

ACL component for Laravel 4.

[![Build Status](https://travis-ci.org/Vivify-Ideas/laravel-acl.png?branch=master)](https://travis-ci.org/Vivify-Ideas/laravel-acl)

- [Installation](#installation)
- [Configuration](#configuration)
  - [Provider](#provider)
  - [Super users](#superusers)
  - [Guest user](#guestuser)
  - [Permissions](#permissions)
  - [Groups](#groups)
- [Usage](#usage)

## Installation

First you need to install this package through Composer. Edit your project's `composer.json` file to require `vivify-ideas/acl`.

```
  "require": {
    "vivify-ideas/acl": "dev-master"
  },
  "minimum-stability" : "dev"
```

Next, update Composer from the Terminal:

```
  composer update
```

Once this operation completes, you will need to add the service provider into your app. Open `app/config/app.php`, and add a new item to the providers array.

```
  'VivifyIdeas\Acl\AclServiceProvider',
```

And also add new alias into aliases array.

```
  'Acl' => 'VivifyIdeas\Acl\Facades\Acl',
```

The last step is to create database structure for keeping ACL. You can do this easily by running the following `artisan` command:

```
php artisan acl:install
```

This will use current permission provider (`Eloquent`) to create necessary DB structure. When finished, you should have six new tables in your database: `acl_permissions`, `acl_groups`, `acl_user_permissions`, `acl_roles`, `acl_roles_permissions` and `acl_users_roles`.

That's it! You're all set to go.

## Configuration

After runing `artisan acl:install` command, you will get a new config file in `app/config/packages/vivify-ideas/acl/config.php`.

There you will notice several different settings.

### Provider

```php
'provider' => 'eloquent'
```

Here you can set the provider class that you want to use. Main feature of this ACL component is `PermissionsProvider`, which abstract storage of permissions. Currently `Eloquent` is the only one permission provider available (you can assume that permissions will be stored in DB that you specified on your project).

### SuperUsers

```php
'superusers' => array()
```

Here you can define user IDs that will have superuser rights. These users will be allowed all permissions.

### GuestUser

```php
'guestuser' => 0
```

Put here ID that will used for setting permissions to guest users.

### Permissions

```php
'permissions' => array()
```

Here you need to put permissions for every resource that will be protected by ACL. Any resource that is not definied here or has its `allowed` field set to `true` will be freely accessable by any authenticated user or possibly a guest. Permissions need to be in following format:

```php
array(
  array(
    'id' => 'PERMISSION_ID',
    'allowed' => true|false,
    'route' => array('GET:/resource/(\d+)/edit', 'PUT:/resource/(\d+)'),
    'resource_id_required' => true|false,
    'name' => 'Permission name',
    'group_id' => 'GROUP_ID_1', // optional
  ), array(
    'id' => 'PERMISSION_ID_2',
    'allowed' => true|false,
    'route' => 'GET:/resource/(\d+)',
    'resource_id_required' => true|false,
    'name' => 'Permission 2 name'
    'group_id' => 'GROUP_ID_2', // optional
  )
 )
```

### Groups

```php
'groups' => array()
```
The purpose of groups is to limit access to groups of resources that share the same base path. For example you want to alllow user to access the page at `admin/products` path
Every permission can belong to a group. You can have groups that belongs to other group. Every group can have a route. Use the following format:

```php
array(
  array(
    'id' => 'ADMIN_PRIVILEGES',
    'name' => 'Administrator Privileges',
    'route' => 'GET:/admin/(\d+)',

    'children' => array(
      array(
        'id' => 'MANAGE_STUFF',
        'name' => 'Manage Stuff',
        'route' => 'GET:/resource/(\d+)'
      ),
      array(
        'id' => 'MANAGE_PRODUCTS',
        'name' => 'Manage Products',
        'route' => 'GET:/resource/(\d+)'
      ),
      array(
        'id' => 'MANAGE_USERS',
        'name' => 'Manage Users',
        'route' => 'GET:/resource/(\d+)',

        'children' => array(
          array(
            'id' => 'MANAGE_SPEC_USER',
            'name' => 'Manage spec user',
            'route' => 'GET:/resource/(\d+)'
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
```
### Roles

```php
'roles' => array()
```
Roles are sets of permissions that can be assigned to different users. <!--- Roles can have parent role and children roles that inherit their permissions. (not yet implemented) -->
Permissions based on roles are applied after general permissions, but before user specific permissions. This means that you can override role based permissions with user specific permissions.

## Usage

When you are satisfied with your configuration file, run the following artisan command:

```
php artisan acl:update
```

This command needs to be run every time you update config file with new permissions and wish to add them to the database.

If you want to delete all permissions (including user permissions), and again reload permissions from config file you can use this command:

```
php artisan acl:reset
```

### Available Artisan commands

Here is the list of all artisan commands:

- ```acl:install``` Create ACL table structure.
- ```acl:install clean``` Delete all acl tables, reset config file to default version and again create ACL table structure.
- ```acl:update``` Update all ACL permissions in the database from config file.
- ```acl:reset``` Reset all ACL permissions. This will delete both user and system permissions and install permissions from config file

### Add Acl Filter To Your Application

Now we need to add appropriate filter to application and to set usage in `routes.php` file.

You can add this filter to your `filters.php` file and adjust it to suit your own needs:

```php
Route::filter('acl', function($route, $request)
{
    // we need this because laravel delete form sends POST request with {_method: 'DELETE'} as parameter
    $method = $request->has('_method') ? $request->input('_method') : $request->server('REQUEST_METHOD');
    
    if (!Acl::checkRoute($method, $request->server('REQUEST_URI'))) {
         App::abort(403);
    }
});
```

And then in `routes.php` use this filter according to your needs.

```php
Route::group(array('before' => 'acl', 'prefix' => '/admin'), function()
{
...
});
```

### Checking permissions

Here are few ways how to check user permissions:

```php
// Whether a user with ID 2 can see a list of all products
Acl::user(2)->permission('LIST_PRODUCTS')->check();

// Whether a user with ID 1 can edit product with ID 2
Acl::user(1)->permission('EDIT_PRODUCT', 2)->check();

// Can currently authenticated user edit product with ID 2
Acl::permission('EDIT_PRODUCT', 2)->check();

// Whether a user with ID 1 can edit and delete product with ID 2
Acl::user(1)->permission('EDIT_PRODUCT', 2)
            ->permission('DELETE_PRODUCT', 2)
            ->check();

// Can user with ID 1 access /products URL
Acl::user(1)->checkRoute('GET', '/products')

// Can currently authenticated user access /products URL
Acl::checkRoute('GET', '/products');

// Get me array of product IDs that user with ID 1 can edit
Acl::user(1)->permission('EDIT_PRODUCT')->getResourceIds();

// Get me array of product IDs that user with ID 1 can not edit
Acl::user(1)->permission('EDIT_PRODUCT')->getResourceIds(false);
```
