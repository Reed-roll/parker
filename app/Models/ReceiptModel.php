<?php

namespace App\Models;

use CodeIgniter\Model;

class ReceiptModel extends Model
{
    protected $table = 'receipts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['payment_id', 'user_id', 'data', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
