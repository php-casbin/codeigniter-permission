<?php

namespace Casbin\CodeIgniter\Tests\Database\Seeds;

use Config\Services;

class CITestSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        // Job Data
        $data = [
            ['ptype' => 'p', 'v0' => 'alice', 'v1' => 'data1', 'v2' => 'read'],
            ['ptype' => 'p', 'v0' => 'bob', 'v1' => 'data2', 'v2' => 'write'],
            ['ptype' => 'p', 'v0' => 'data2_admin', 'v1' => 'data2', 'v2' => 'read'],
            ['ptype' => 'p', 'v0' => 'data2_admin', 'v1' => 'data2', 'v2' => 'write'],
            ['ptype' => 'g', 'v0' => 'alice', 'v1' => 'data2_admin'],
        ];
        $config = Services::enforcer()->getDefaultConfig();
        $table = $config['database']['rules_table'];

        $this->db->table($table)->truncate();

        foreach ($data as $single_dummy_data) {
            $this->db->table($table)->insert($single_dummy_data);
        }
    }

    //--------------------------------------------------------------------
}
