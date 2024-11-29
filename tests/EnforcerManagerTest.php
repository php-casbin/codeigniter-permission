<?php

namespace Casbin\CodeIgniter\Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services
;
use Config\Autoload;
use Config\Modules;

class EnforcerManagerTest extends CIUnitTestCase
{    
    use DatabaseTestTrait;

    protected function createApplication()
    {
        $app = parent::createApplication();
        
        Services::autoloader()->addNamespace('Casbin\CodeIgniter', dirname(__DIR__).'/src');

        Services::cache()->clean();

        return $app;
    }

    /**
     * The path to where we can find the seeds directory.
     * Allows overriding the default application directories.
     *
     * @var string
     */
    protected $basePath = __DIR__.'/Database';

    protected $seed = '\Casbin\CodeIgniter\Tests\Database\Seeds\CITestSeeder';

    /**
     * The namespace to help us find the migration classes.
     *
     * @var string
     */
    protected $namespace = 'Casbin\CodeIgniter';

    public function testEnforce()
    {
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data1', 'read'));

        $this->assertFalse(Services::enforcer()->enforce('bob', 'data1', 'read'));
        $this->assertTrue(Services::enforcer()->enforce('bob', 'data2', 'write'));

        $this->assertTrue(Services::enforcer()->enforce('alice', 'data2', 'read'));
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data2', 'write'));
    }

    public function testAddPolicy()
    {
        $this->assertFalse(Services::enforcer()->enforce('eve', 'data3', 'read'));
        Services::enforcer()->addPermissionForUser('eve', 'data3', 'read');
        $this->assertTrue(Services::enforcer()->enforce('eve', 'data3', 'read'));
    }

    public function testSavePolicy()
    {
        $this->assertFalse(Services::enforcer()->enforce('alice', 'data4', 'read'));

        $model = Services::enforcer()->getModel();
        // $model->clearPolicy();
        $model->addPolicy('p', 'p', ['alice', 'data4', 'read']);

        $adapter = Services::enforcer()->getAdapter();
        $adapter->savePolicy($model);
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data4', 'read'));
    }

    public function testRemovePolicy()
    {
        $this->assertFalse(Services::enforcer()->enforce('alice', 'data5', 'read'));

        Services::enforcer()->addPermissionForUser('alice', 'data5', 'read');
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data5', 'read'));

        Services::enforcer()->deletePermissionForUser('alice', 'data5', 'read');
        $this->assertFalse(Services::enforcer()->enforce('alice', 'data5', 'read'));
    }

    public function testRemoveFilteredPolicy()
    {
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data1', 'read'));
        Services::enforcer()->removeFilteredPolicy(1, 'data1');
        $this->assertFalse(Services::enforcer()->enforce('alice', 'data1', 'read'));
        $this->assertTrue(Services::enforcer()->enforce('bob', 'data2', 'write'));
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data2', 'read'));
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data2', 'write'));
        Services::enforcer()->removeFilteredPolicy(1, 'data2', 'read');
        $this->assertTrue(Services::enforcer()->enforce('bob', 'data2', 'write'));
        $this->assertFalse(Services::enforcer()->enforce('alice', 'data2', 'read'));
        $this->assertTrue(Services::enforcer()->enforce('alice', 'data2', 'write'));
        Services::enforcer()->removeFilteredPolicy(2, 'write');
        $this->assertFalse(Services::enforcer()->enforce('bob', 'data2', 'write'));
        $this->assertFalse(Services::enforcer()->enforce('alice', 'data2', 'write'));
    }
}
