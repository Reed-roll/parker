<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Create_parking_tables extends Migration
{
    public function up()
    {
        // users
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'TEXT', 'null' => false],
            'password_hash' => ['type' => 'TEXT', 'null' => true],
            'full_name' => ['type' => 'TEXT', 'null' => true],
            'phone' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // parking_spots
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'label' => ['type' => 'TEXT', 'null' => false],
            'location' => ['type' => 'TEXT', 'null' => true],
            'is_available' => ['type' => 'INTEGER', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('parking_spots', true);

        // tickets
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INTEGER', 'null' => false],
            'parking_spot_id' => ['type' => 'INTEGER', 'null' => true],
            'license_plate' => ['type' => 'TEXT', 'null' => true],
            'start_time' => ['type' => 'DATETIME', 'null' => false],
            'end_time' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'TEXT', 'null' => false, 'default' => 'active'],
            'amount_due' => ['type' => 'DOUBLE', 'null' => false, 'default' => 0],
            'amount_paid' => ['type' => 'DOUBLE', 'null' => false, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tickets', true);

        // payments
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ticket_id' => ['type' => 'INTEGER', 'null' => false],
            'user_id' => ['type' => 'INTEGER', 'null' => false],
            'amount' => ['type' => 'DOUBLE', 'null' => false],
            'method' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'TEXT', 'null' => true],
            'transaction_id' => ['type' => 'TEXT', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payments', true);

        // receipts
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'payment_id' => ['type' => 'INTEGER', 'null' => false],
            'user_id' => ['type' => 'INTEGER', 'null' => false],
            'data' => ['type' => 'TEXT', 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('receipts', true);
    }

    public function down()
    {
        $this->forge->dropTable('receipts', true);
        $this->forge->dropTable('payments', true);
        $this->forge->dropTable('tickets', true);
        $this->forge->dropTable('parking_spots', true);
        $this->forge->dropTable('users', true);
    }
}
