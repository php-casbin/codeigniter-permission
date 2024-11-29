<?php

namespace Casbin\CodeIgniter;

use Casbin\Enforcer;
use Casbin\Model\Model;
use Casbin\Log\Log;
use Casbin\Log\Logger\DefaultLogger;
use Config\Services;
use InvalidArgumentException;
use Casbin\CodeIgniter\Config\Enforcer as EnforcerConfig;

/**
 * @mixin \Casbin\Enforcer
 */
class EnforcerManager
{
    /**
     * The config instance.
     *
     * @var EnforcerConfig
     */
    protected $config;

    /**
     * The array of created "guards".
     *
     * @var array
     */
    protected $guards = [];

    /**
     * Create a new manager instance.
     *
     * @param EnforcerConfig $config
     */
    public function __construct(EnforcerConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Attempt to get the enforcer from the local cache.
     *
     * @param string $name
     *
     * @return \Casbin\Enforcer
     *
     * @throws \InvalidArgumentException
     */
    public function guard($name = null)
    {
        $name = $name ?: $this->getDefaultGuard();

        if (!isset($this->guards[$name])) {
            $this->guards[$name] = $this->resolve($name);
        }

        return $this->guards[$name];
    }

    /**
     * Resolve the given guard.
     *
     * @param string $name
     *
     * @return \Casbin\Enforcer
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Enforcer [{$name}] is not defined.");
        }

        if ($logger = $config['log']['logger']) {
            if (is_string($logger)) {
                $logger = new DefaultLogger(Services::$logger());
            }

            Log::setLogger($logger);
        }

        $model = new Model();
        $configType = $config['model']['config_type'];
        if ('file' == $configType) {
            $model->loadModel($config['model']['config_file_path']);
        } elseif ('text' == $configType) {
            $model->loadModelFromText($config['model']['config_text']);
        }
        $adapter = $config['adapter'];
        if (!is_null($adapter)) {
            if (is_string($adapter)) {
                $adapter = new $adapter($config);
            }
        }

        return new Enforcer($model, $adapter, $logger, $config['log']['enabled']);
    }

    /**
     * Get the enforcer driver configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name): array
    {
        return $this->config->{$name};
    }

    /**
     * Get the default enforcer guard name.
     *
     * @return string
     */
    public function getDefaultGuard()
    {
        return $this->config->default;
    }

    /**
     * Set the default guard driver the factory should serve.
     *
     * @param string $name
     */
    public function shouldUse($name)
    {
        $name = $name ?: $this->getDefaultGuard();

        $this->setDefaultGuard($name);
    }

    /**
     * Set the default authorization guard name.
     *
     * @param string $name
     */
    public function setDefaultGuard($name)
    {
        $this->config->default = $name;
    }

    /**
     * gets default config for enforcer.
     *
     * @param string $name
     *
     * @return void
     */
    public function getDefaultConfig()
    {
        $name = $this->getDefaultGuard();

        return $this->config->{$name};
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->guard()->{$method}(...$parameters);
    }
}
