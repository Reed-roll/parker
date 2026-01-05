<?php

namespace App\Controllers;

use App\Libraries\PaymentClient;
use App\Models\PaymentModel;
use App\Models\TicketModel;
use CodeIgniter\RESTful\ResourceController;

class PaymentController extends ResourceController
{
    protected $format = 'json';

    public function pay($ticketId = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = $this->request->getHeaderLine('X-User-Id') ?: ($data['user_id'] ?? null);
        if (!$userId || !$ticketId) {
            return $this->respond(['error' => 'user_id and ticketId required'], 400);
        }

        $ticketModel = new TicketModel();
        $ticket = $ticketModel->find($ticketId);
        if (!$ticket) {
            return $this->failNotFound('Ticket not found');
        }

        $amount = $data['amount'] ?? $ticket['amount_due'];

        $client = new PaymentClient();
        $resp = $client->charge([
            'ticket_id' => $ticketId,
            'user_id' => (int)$userId,
            'amount' => $amount,
            'method' => $data['method'] ?? 'card',
        ]);

        $paymentModel = new PaymentModel();
        $paymentId = $paymentModel->insert([
            'ticket_id' => $ticketId,
            'user_id' => (int)$userId,
            'amount' => $amount,
            'method' => $data['method'] ?? 'card',
            'status' => $resp['status'] ?? 'failed',
            'transaction_id' => $resp['transaction_id'] ?? ($resp['transactionId'] ?? null),
            'paid_at' => ($resp['status'] ?? '') === 'completed' ? date('Y-m-d H:i:s') : null,
            'created_at' => date('Y-m-d H:i:s'),
        ], true);

        if (($resp['status'] ?? '') === 'completed') {
            // update ticket
            $paid = $ticket['amount_paid'] + $amount;
            $status = ($paid >= $ticket['amount_due']) ? 'paid' : 'partially_paid';
            $ticketModel->update($ticketId, ['amount_paid' => $paid, 'status' => $status]);
        }

        $payment = $paymentModel->find($paymentId);
        return $this->respondCreated(['payment' => $payment, 'raw' => $resp]);
    }

    public function webhook()
    {
        // Accept webhooks from payment microservice to mark payments as completed
        $payload = $this->request->getJSON(true) ?? [];
        $transactionId = $payload['transaction_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$transactionId) {
            return $this->respond(['error' => 'transaction_id required'], 400);
        }

        $paymentModel = new PaymentModel();
        $payment = $paymentModel->where('transaction_id', $transactionId)->first();
        if (!$payment) {
            return $this->failNotFound('Payment not found');
        }

        $paymentModel->update($payment['id'], ['status' => $status, 'paid_at' => date('Y-m-d H:i:s')]);

        if ($status === 'completed') {
            $ticketModel = new TicketModel();
            $ticket = $ticketModel->find($payment['ticket_id']);
            if ($ticket) {
                $paid = $ticket['amount_paid'] + $payment['amount'];
                $status = ($paid >= $ticket['amount_due']) ? 'paid' : 'partially_paid';
                $ticketModel->update($ticket['id'], ['amount_paid' => $paid, 'status' => $status]);
            }
        }

        return $this->respond(['ok' => true]);
    }
}
