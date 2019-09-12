<?php

namespace Casbin\CodeIgniter\Adapters;

use Casbin\Persist\Adapter;
use Casbin\Persist\AdapterHelper;
use Casbin\CodeIgniter\Models\RuleModel;
use Config\Database;

/**
 * DatabaseAdapter.
 *
 * @author techlee@qq.com
 */
class DatabaseAdapter implements Adapter
{
    use AdapterHelper;

    /**
     * RuleModel instance.
     *
     * @var RuleModel
     */
    protected $model;

    /**
     * the DatabaseAdapter constructor.
     *
     * @param RuleModel $model
     */
    public function __construct(array $config)
    {
        $db = null;
        if ($connection = $config['database']['connection']) {
            $db = Database::connect($connection);
        }

        $this->model = new RuleModel($db);
        $this->model->setTable($config['database']['rules_table']);
        $this->model->setCacheConfig($config['cache']);
    }

    /**
     * savePolicyLine function.
     *
     * @param string $ptype
     * @param array  $rule
     */
    public function savePolicyLine($ptype, array $rule)
    {
        $col['ptype'] = $ptype;
        foreach ($rule as $key => $value) {
            $col['v'.strval($key)] = $value;
        }

        $this->model->insert($col);
    }

    /**
     * loads all policy rules from the storage.
     *
     * @param Model $model
     *
     * @return mixed
     */
    public function loadPolicy($model)
    {
        $rows = $this->model->getAllFromCache();        

        foreach ($rows as $row) {
            $line = implode(', ', array_filter($row, function ($val) {
                return '' != $val && !is_null($val);
            }));
            $this->loadPolicyLine(trim($line), $model);
        }
    }

    /**
     * saves all policy rules to the storage.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function savePolicy($model)
    {
        foreach ($model->model['p'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }

        foreach ($model->model['g'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }

        return true;
    }

    /**
     * Adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     *
     * @return mixed
     */
    public function addPolicy($sec, $ptype, $rule)
    {
        return $this->savePolicyLine($ptype, $rule);
    }

    /**
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     *
     * @return mixed
     */
    public function removePolicy($sec, $ptype, $rule)
    {
        $count = 0;

        $instance = $this->model->where('ptype', $ptype);

        foreach ($rule as $key => $value) {
            $instance->where('v'.strval($key), $value);
        }

        foreach ($instance->findAll() as $model) {
            if ($this->model->delete($model['id'])) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * RemoveFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param mixed  ...$fieldValues
     *
     * @return mixed
     */
    public function removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues)
    {
        $count = 0;

        $instance = $this->model->where('ptype', $ptype);
        foreach (range(0, 5) as $value) {
            if ($fieldIndex <= $value && $value < $fieldIndex + count($fieldValues)) {
                if ('' != $fieldValues[$value - $fieldIndex]) {
                    $instance->where('v'.strval($value), $fieldValues[$value - $fieldIndex]);
                }
            }
        }

        foreach ($instance->findAll() as $model) {
            if ($this->model->delete($model['id'])) {
                ++$count;
            }
        }

        return $count;
    }
}
