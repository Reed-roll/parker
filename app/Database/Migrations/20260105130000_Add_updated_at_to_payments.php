<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Add_updated_at_to_payments extends Migration
{
    public function up()
    {
        $fields = [
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];
        $this->forge->addColumn('payments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('payments', 'updated_at');
    }
}