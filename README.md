Laravel ACL
===========

ACL component for Laravel 4.

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
  'VivifyIdeas\Acl\AclServiceProvider
```

And also add two new aliases into aliases array.

```
  'Acl'        => 'VivifyIdeas\Acl\Facades\Checker',
  'AclManager' => 'VivifyIdeas\Acl\Facades\Manager',
```

That's it! You're all set to go.


## Usage

Main feature of this ACL component is `PermissionsProvider`. Permission provider represent class that handle permissions. Currently there is only one permission provider `Eloquent` (you can assume that permissions will be stored in DB that you specified on your project).

First step after installing this component is to create main structure for keeping ACL. You can easy done this by running `artisan` command:

```
php artisan acl:install
```

This will use current permission provider (`Eloquent`) and create DB structure for saving permissions. It will create 2 additional tables `acl_permissions` and `acl_user_permissions`.

Next step is to fill available permission for your application. You can do this by overwriting ACL default config file (`app/config/packages/vivify-ideas/acl/config.php`).

In the config file there is `permissions` section that you need to put all permissions that exist in your system in `permissions` section.

When you are satisfy how your permissions look like, run next artisan command:

```
php artisan acl:update
```

This command you need to run every time when you update config file with new permissions.

If you want to delete all permissions (including user permissions), and again reload permissions from config file you can use this command:

```
php artisan acl:reset
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
