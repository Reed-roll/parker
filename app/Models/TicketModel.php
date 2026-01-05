<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'parking_spot_id', 'license_plate', 'start_time', 'end_time', 'status', 'amount_due', 'amount_paid', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
