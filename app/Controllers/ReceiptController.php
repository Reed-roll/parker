<?php

namespace App\Controllers;

use App\Models\ReceiptModel;
use App\Models\PaymentModel;
use CodeIgniter\RESTful\ResourceController;

class ReceiptController extends ResourceController
{
    protected $format = 'json';

    public function show($paymentId = null)
    {
        if (!$paymentId) {
            return $this->respond(['error' => 'payment id required'], 400);
        }
        $receiptModel = new ReceiptModel();
        $receipt = $receiptModel->where('payment_id', (int)$paymentId)->first();
        if ($receipt) {
            return $this->respond($receipt);
        }

        // Build a simple receipt from payment if not present
        $paymentModel = new PaymentModel();
        $payment = $paymentModel->find($paymentId);
        if (!$payment) {
            return $this->failNotFound('Payment not found');
        }

        $data = [
            'payment_id' => $payment['id'],
            'user_id' => $payment['user_id'],
            'data' => json_encode(['amount' => $payment['amount'], 'transaction_id' => $payment['transaction_id'], 'paid_at' => $payment['paid_at']]),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id = $receiptModel->insert($data, true);
        return $this->respondCreated($receiptModel->find($id));
    }
}
