<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ticket_id', 'user_id', 'amount', 'method', 'status', 'transaction_id', 'paid_at', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
