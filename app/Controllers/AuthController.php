<?php
namespace App\Controllers;

use App\Libraries\JWT;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    protected $format = 'json';

    protected function jwtSecret(): string
    {
        $s = getenv('JWT_SECRET');
        return $s ? $s : 'change-me-in-production';
    }

    public function register()
    {
        $data = $this->request->getJSON(true) ?: [];
        if (empty($data['email']) || empty($data['password'])) {
            return $this->fail('email and password required', 400);
        }
        $model = new UserModel();
        $exists = $model->where('email', $data['email'])->first();
        if ($exists) return $this->fail('user exists', 409);

        $userId = $model->insert([
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'full_name' => $data['full_name'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
        $user = $model->find($userId);
        unset($user['password_hash']);
        return $this->respondCreated(['user' => $user]);
    }

    public function login()
    {
        $data = $this->request->getJSON(true) ?: [];
        if (empty($data['email']) || empty($data['password'])) {
            return $this->fail('email and password required', 400);
        }
        $model = new UserModel();
        $user = $model->where('email', $data['email'])->first();
        if (!$user || !isset($user['password_hash']) || !password_verify($data['password'], $user['password_hash'])) {
            return $this->fail('invalid credentials', 401);
        }
        $payload = ['sub' => $user['id'], 'email' => $user['email']];
        $token = JWT::encode($payload, $this->jwtSecret(), 60*60*24);
        unset($user['password_hash']);
        return $this->respond(['token' => $token, 'user' => $user]);
    }

    public function me()
    {
        $id = $this->request->getHeaderLine('X-User-Id') ?: null;
        if (!$id) return $this->failUnauthorized('No authenticated user');
        $model = new UserModel();
        $user = $model->find($id);
        if (!$user) return $this->failNotFound('User not found');
        unset($user['password_hash']);
        return $this->respond($user);
    }
}
