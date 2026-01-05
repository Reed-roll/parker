<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\ParkingSpotModel;
use CodeIgniter\RESTful\ResourceController;

class TicketController extends ResourceController
{
    protected $format = 'json';

    public function start()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = $this->request->getHeaderLine('X-User-Id') ?: ($data['user_id'] ?? null);
        if (!$userId) {
            return $this->respond(['error' => 'user_id required'], 400);
        }

        $ticketModel = new TicketModel();
        $parkingSpotId = $data['parking_spot_id'] ?? null;
        $now = date('Y-m-d H:i:s');

        $ticketId = $ticketModel->insert([
            'user_id' => (int)$userId,
            'parking_spot_id' => $parkingSpotId ? (int)$parkingSpotId : null,
            'license_plate' => $data['license_plate'] ?? null,
            'start_time' => $now,
            'status' => 'active',
        ], true);

        if ($parkingSpotId) {
            $ps = new ParkingSpotModel();
            $ps->update($parkingSpotId, ['is_available' => 0]);
        }

        $ticket = $ticketModel->find($ticketId);
        return $this->respondCreated($ticket);
    }

    public function end($id = null)
    {
        $ticketModel = new TicketModel();
        $ticket = $ticketModel->find($id);
        if (!$ticket) {
            return $this->failNotFound('Ticket not found');
        }

        if ($ticket['end_time']) {
            return $this->respond(['message' => 'Ticket already ended', 'ticket' => $ticket]);
        }

        $now = date('Y-m-d H:i:s');
        $start = strtotime($ticket['start_time']);
        $end = strtotime($now);
        $seconds = max(0, $end - $start);
        $hours = ceil($seconds / 3600);
        $rate = 2.50; // default per-hour rate; could be configurable
        $amountDue = $hours * $rate;

        $ticketModel->update($id, ['end_time' => $now, 'amount_due' => $amountDue, 'status' => 'ended']);

        // free up spot
        if ($ticket['parking_spot_id']) {
            $ps = new ParkingSpotModel();
            $ps->update($ticket['parking_spot_id'], ['is_available' => 1]);
        }

        $ticket = $ticketModel->find($id);
        return $this->respond(['ticket' => $ticket]);
    }

    public function show($id = null)
    {
        $ticketModel = new TicketModel();
        $ticket = $ticketModel->find($id);
        if (!$ticket) {
            return $this->failNotFound('Ticket not found');
        }
        return $this->respond($ticket);
    }
}
