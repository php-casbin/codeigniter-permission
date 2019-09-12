<?php

namespace Casbin\CodeIgniter\Models;

use CodeIgniter\Model;

class RuleModel extends Model
{
    protected $afterInsert = ['refreshCache'];

    protected $afterUpdate = ['refreshCache'];

    protected $afterDelete = ['refreshCache'];

    protected $cacheConfig = [
        'enabled' => false,
        'key' => 'rules',
        'ttl' => 24 * 60,
    ];

    protected $allowedFields = ['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'];

    /**
     * Gets rules from caches.
     *
     * @return mixed
     */
    public function getAllFromCache()
    {
        $get = function () {
            return $this->select('ptype, v0, v1, v2, v3, v4, v5')->findAll();
        };
        if (!($this->cacheConfig['enabled'] ?? false)) {
            return $get();
        }
        
        if (!$rules = cache($this->cacheConfig['key'])) {
            $rules = $get();
            cache()->save($this->cacheConfig['key'], $rules, $this->cacheConfig['ttl']);
        }

        return $rules;
    }

    /**
     * Refresh Cache.
     */
    protected function refreshCache(...$args)
    {
        if (!($this->cacheConfig['enabled'] ?? false)) {
            return;
        }

        $this->forgetCache();
        $this->getAllFromCache();
    }

    /**
     * Forget Cache.
     */
    public function forgetCache()
    {
        cache()->delete($this->cacheConfig['key']);
    }

    /**
     * sets cacheConfig.
     *
     * @param array $cacheConfig
     *
     * @return void
     */
    public function setCacheConfig(array $cacheConfig = [])
    {
        if ($cacheConfig) {
            $this->cacheConfig = $cacheConfig;
        }
    }
}
