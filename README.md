<h1 align="center">
    CodeIgniter Permission
</h1>

<p align="center">
    <strong>CodeIgniter Permission is an authorization library for the CodeIgniter4 framework.</strong>    
</p>

<p align="center">
    <a href="https://travis-ci.org/php-casbin/codeigniter-permission">
        <img src="https://travis-ci.org/php-casbin/codeigniter-permission.svg?branch=master" alt="Build Status">
    </a>
    <a href="https://coveralls.io/github/php-casbin/codeigniter-permission">
        <img src="https://coveralls.io/repos/github/php-casbin/codeigniter-permission/badge.svg" alt="Coverage Status">
    </a>
    <a href="https://packagist.org/packages/casbin/codeigniter-permission">
        <img src="https://poser.pugx.org/casbin/codeigniter-permission/v/stable" alt="Latest Stable Version">
    </a>
     <a href="https://packagist.org/packages/casbin/codeigniter-permission">
        <img src="https://poser.pugx.org/casbin/codeigniter-permission/downloads" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/casbin/codeigniter-permission">
        <img src="https://poser.pugx.org/casbin/codeigniter-permission/license" alt="License">
    </a>
</p>

It's based on [Casbin](https://github.com/php-casbin/php-casbin), an authorization library that supports access control models like ACL, RBAC, ABAC.

All you need to learn to use `Casbin` first.

* [Installation](#installation)
* [Usage](#usage)
  * [Quick start](#quick-start)
  * [Using Enforcer Api](#using-enforcer-api)  
  * [Multiple enforcers](#multiple-enforcers)  
  * [Cache](#using-cache)
* [Thinks](#thinks)
* [License](#license)

## Installation

Require this package in the `composer.json` of your Laravel project. This will download the package.

```
composer require casbin/codeigniter-permission
```

To migrate the migrations, run the migrate command:

```
php spark migrate -n "Casbin\CodeIgniter"
```

This will create a new table named `rules`


## Usage

### Quick start

Once installed you can do stuff like this:

```php

$enforcer = \Config\Services::enforcer();

// adds permissions to a user
$enforcer->addPermissionForUser('eve', 'articles', 'read');
// adds a role for a user.
$enforcer->addRoleForUser('eve', 'writer');
// adds permissions to a rule
$enforcer->addPolicy('writer', 'articles','edit');

```

You can check if a user has a permission like this:

```php
// to check if a user has permission
if ($enforcer->enforce("eve", "articles", "edit")) {
    // permit eve to edit articles
} else {
    // deny the request, show an error
}

```

### Using Enforcer Api

It provides a very rich api to facilitate various operations on the Policy:

Gets all roles:

```php
$enforcer->getAllRoles(); // ['writer', 'reader']
```

Gets all the authorization rules in the policy.:

```php
$enforcer->getPolicy();
```

Gets the roles that a user has.

```php
$enforcer->getRolesForUser('eve'); // ['writer']
```

Gets the users that has a role.

```php
$enforcer->getUsersForRole('writer'); // ['eve']
```

Determines whether a user has a role.

```php
$enforcer->hasRoleForUser('eve', 'writer'); // true or false
```

Adds a role for a user.

```php
$enforcer->addRoleForUser('eve', 'writer');
```

Adds a permission for a user or role.

```php
// to user
$enforcer->addPermissionForUser('eve', 'articles', 'read');
// to role
$enforcer->addPermissionForUser('writer', 'articles','edit');
```

Deletes a role for a user.

```php
$enforcer->deleteRoleForUser('eve', 'writer');
```

Deletes all roles for a user.

```php
$enforcer->deleteRolesForUser('eve');
```

Deletes a role.

```php
$enforcer->deleteRole('writer');
```

Deletes a permission.

```php
$enforcer->deletePermission('articles', 'read'); // returns false if the permission does not exist (aka not affected).
```

Deletes a permission for a user or role.

```php
$enforcer->deletePermissionForUser('eve', 'articles', 'read');
```

Deletes permissions for a user or role.

```php
// to user
$enforcer->deletePermissionsForUser('eve');
// to role
$enforcer->deletePermissionsForUser('writer');
```

Gets permissions for a user or role.

```php
$enforcer->getPermissionsForUser('eve'); // return array
```

Determines whether a user has a permission.

```php
$enforcer->hasPermissionForUser('eve', 'articles', 'read');  // true or false
```

See [Casbin API](https://casbin.org/docs/en/management-api) for more APIs.

### Multiple enforcers

If you need multiple permission controls in your project, you can configure multiple enforcers.

In the `Config\Enforcer.php` file, it should be like this:

```php

namespace Config;

use Casbin\CodeIgniter\Config as BaseConfig;
use Casbin\CodeIgniter\Adapters\DatabaseAdapter;

class Enforcer extends BaseConfig
{
    /*
     * Default Enforcer driver
     *
     * @var string
     */
    public $default = 'basic';

    public $basic = [
        /*
        * Casbin model setting.
        */
        'model' => [
            // Available Settings: "file", "text"
            'config_type' => 'file',

            'config_file_path' => __DIR__.'/lauthz-rbac-model.conf',

            'config_text' => '',
        ],

        /*
        * Casbin adapter .
        */
        'adapter' => DatabaseAdapter::class,

        /*
        * Database setting.
        */
        'database' => [
            // Database connection for following tables.
            'connection' => '',

            // Rule table name.
            'rules_table' => 'rules',
        ],

        'log' => [
            // changes whether Casbin will log messages to the Logger.
            'enabled' => false,

            // Casbin Logger
            'logger' => \Casbin\CodeIgniter\Logger::class,
        ],

        'cache' => [
            // changes whether Casbin will cache the rules.
            'enabled' => false,

            // cache Key
            'key' => 'rules',

            // ttl int|null
            'ttl' => 24 * 60,
        ],
    ];

    public $second = [
        'model' => [
            // ...
        ],

        'adapter' => DatabaseAdapter::class,
        // ...
    ];
}

```

Then you can choose which enforcers to use.

```php
$enforcer->guard('second')->enforce("eve", "articles", "edit");
```

### Using cache

Authorization rules are cached to speed up performance. The default is off.

Sets your own cache configs in `Config\Enforcer.php`. 

```php
'cache' => [
    // changes whether Casbin will cache the rules.
    'enabled' => false,
    // cache Key
    'key' => 'rules',
    // ttl int|null
    'ttl' => 24 * 60,
]
```

## Thinks

[Casbin](https://github.com/php-casbin/php-casbin) in Laravel. You can find the full documentation of Casbin [on the website](https://casbin.org/).

## License

This project is licensed under the [Apache 2.0 license](LICENSE).
