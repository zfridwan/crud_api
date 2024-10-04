<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    private $key = "secretkey";

    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

       
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('email', $email)->get()->getRow();

        if (!$user || !password_verify($password, $user->password)) {
            return $this->respond(['message' => 'Invalid login'], 401);
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ];
        $token = JWT::encode($payload, $this->key, 'HS256');

        return $this->respond(['token' => $token], 200);
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return false;
        }
    }
}
