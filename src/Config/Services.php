<?php

namespace Casbin\CodeIgniter\Config;

use CodeIgniter\Config\BaseService;
use Casbin\CodeIgniter\EnforcerManager;

class Services extends BaseService
{
    /**
     * The enforcer Services.
     *
     * @param \Casbin\CodeIgniter\Config\Enforcer $config
     * @param bool                                $getShared
     *
     * @return \Casbin\CodeIgniter\EnforcerManager
     */
    public static function enforcer(Enforcer $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('enforcer', $config);
        }

        if (!is_object($config)) {
            if (class_exists('Config\Enforcer')) {
                $config = new \Config\Enforcer();
            } else {
                $config = new Enforcer();
            }
        }

        return new EnforcerManager($config);
    }
}
