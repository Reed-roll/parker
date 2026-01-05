<?php

namespace App\Models;

use CodeIgniter\Model;

class ParkingSpotModel extends Model
{
    protected $table = 'parking_spots';
    protected $primaryKey = 'id';
    protected $allowedFields = ['label', 'location', 'is_available', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
