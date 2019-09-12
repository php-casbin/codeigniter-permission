<?php

namespace Casbin\CodeIgniter\Config;

use CodeIgniter\Config\BaseConfig;
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
}
