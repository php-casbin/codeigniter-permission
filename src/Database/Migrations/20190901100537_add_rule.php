<?php

namespace Casbin\CodeIgniter\Database\Migrations;

use Config\Services;

class AddRule extends \CodeIgniter\Database\Migration
{
    public function up()
    {
        $config = Services::enforcer()->getDefaultConfig();
        if ($config['database']['connection']){
            $this->DBGroup = $config['database']['connection'];
        }
        $this->forge->addField([
             'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'ptype' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'v0' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
			],
			'v1' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
			],
			'v2' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
			],
			'v3' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
			],
			'v4' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
			],
			'v5' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable($config['database']['rules_table']);
    }

    public function down()
    {
        $config = Services::enforcer()->getDefaultConfig();
        if ($config['database']['connection']){
            $this->DBGroup = $config['database']['connection'];
        }
        $this->forge->dropTable($config['database']['rules_table']);
    }
}
