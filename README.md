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
