<?php

namespace App\Controllers;

use App\Models\TicketModel;
use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    protected $format = 'json';

    public function tickets($userId = null)
    {
        if (!$userId) {
            return $this->respond(['error' => 'user id required'], 400);
        }
        $ticketModel = new TicketModel();
        $tickets = $ticketModel->where('user_id', (int)$userId)->findAll();
        return $this->respond($tickets);
    }
}
