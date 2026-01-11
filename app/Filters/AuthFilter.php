<?php
namespace App\Filters;

use App\Libraries\JWT;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    protected function secret(): string
    {
        $s = getenv('JWT_SECRET');
        return $s ? $s : 'change-me-in-production';
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');
        // allow unauthenticated access to auth endpoints and payment webhook
        if (str_starts_with($path, 'api/auth/') || $path === 'api/payments/webhook') {
            return null;
        }

        $auth = $request->getHeaderLine('Authorization') ?: $request->getServer('HTTP_AUTHORIZATION');
        if (!$auth) return \Config\Services::response()->setStatusCode(401, 'Missing Authorization');
        if (stripos($auth, 'Bearer ') === 0) {
            $token = trim(substr($auth, 7));
            $payload = JWT::decode($token, $this->secret());
            if (!$payload || empty($payload['sub'])) {
                return \Config\Services::response()->setStatusCode(401, 'Invalid token');
            }
            // Set X-User-Id for controllers that read it
            $_SERVER['HTTP_X_USER_ID'] = (string)$payload['sub'];
            return null;
        }
        return \Config\Services::response()->setStatusCode(401, 'Invalid Authorization header');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // noop
    }
}
