<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class UserController extends ResourceController
{
    private $key = "secretkey";

    public function __construct()
    {
        $this->model = new \App\Models\UserModel();
    }

    private function getUserData()
    {
        $header = $this->request->getHeader('Authorization');
        $token = $header->getValue();
        $auth = new AuthController();
        return $auth->validateToken($token);
    }

    public function index()
    {
        $userData = $this->getUserData();
        if (!$userData) {
            return $this->respond(['message' => 'Unauthorized'], 401);
        }

        $users = $this->model->findAll();
        return $this->respond($users);
    }

    public function create()
    {
        $userData = $this->getUserData();
        if (!$userData) {
            return $this->respond(['message' => 'Unauthorized'], 401);
        }

        $data = [
            'name'     => $this->request->getVar('name'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT)
        ];

        if ($this->model->insert($data)) {
            return $this->respondCreated(['message' => 'User created successfully']);
        } else {
            return $this->respond(['message' => 'Failed to create user'], 400);
        }
    }

    public function update($id = null)
    {
        $userData = $this->getUserData();
        if (!$userData) {
            return $this->respond(['message' => 'Unauthorized'], 401);
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT)
        ];

        if ($this->model->update($id, $data)) {
            return $this->respond(['message' => 'User updated successfully']);
        } else {
            return $this->respond(['message' => 'Failed to update user'], 400);
        }
    }

    public function delete($id = null)
    {
        $userData = $this->getUserData();
        if (!$userData) {
            return $this->respond(['message' => 'Unauthorized'], 401);
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'User deleted successfully']);
        } else {
            return $this->respond(['message' => 'Failed to delete user'], 400);
        }
    }
}
