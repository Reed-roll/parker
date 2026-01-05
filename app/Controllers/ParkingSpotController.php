<?php

namespace App\Controllers;

use App\Models\ParkingSpotModel;
use CodeIgniter\RESTful\ResourceController;

class ParkingSpotController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $model = new ParkingSpotModel();
        return $this->respond($model->findAll());
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        if (!$id) {
            return $this->respond(['error' => 'id required'], 400);
        }
        $model = new ParkingSpotModel();
        $model->update($id, $data);
        return $this->respond($model->find($id));
    }
}
