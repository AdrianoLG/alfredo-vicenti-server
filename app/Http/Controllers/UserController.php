<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    private $request;

    public function __construct(UserService $userService, Request $request)
    {
        $this->userService = $userService;
        $this->request = $request;
    }

    public function createUser()
    {
        if (!$this->request->has('name')) {
            return $this->missingFieldResponse('name');
        }
        if (!$this->request->has('email')) {
            return $this->missingFieldResponse('email');
        }
        if (!$this->request->has('password')) {
            return $this->missingFieldResponse('password');
        }

        $this->userService->postUser($this->request->all());

        return $this->successResponse(201, 'User succesfully created');
    }

    public function loginUser()
    {
        if (!$this->request->has('email')) {
            return $this->missingFieldResponse('email');
        }
        if (!$this->request->has('password')) {
            return $this->missingFieldResponse('password');
        }

        $grant_type = env('GRANT_TYPE');
        $client_id = env('CLIENT_ID');
        $client_secret = env('CLIENT_SECRET');
        $email = $this->request->email;
        $password = $this->request->password;

        $userData = $this->userService->postUserLogin($grant_type, $client_id, $client_secret, $email, $password);
        return $this->successResponse(200, null, $userData);
    }

    public function getUser(int $id)
    {
        $user = $this->userService->getUser($id);

        if (!is_null($user)) {
            return $this->successResponse(200, null, $user);
        }
        return $this->errorResponse(404, 'No user found with that ID');
    }

    public function updateUserPassword(int $id)
    {
        if (!$this->request->has('password')) {
            return $this->missingFieldResponse('password');
        }
        if (!$this->request->has('password_update_token')) {
            return $this->missingFieldResponse('password_update_token');
        }

        if ($this->userService->putUserPassword($this->request->password, $this->request->password_update_token, $id)) {
            return $this->successResponse(200, 'Password succesfully updated');
        }
    }

    public function updateUserPasswordToken()
    {
        if (!$this->request->has('email')) {
            return $this->missingFieldResponse('email');
        }
        $pass_token = $this->userService->putUserPasswordUpdateToken($this->request->email);

        if (!is_null($pass_token)) {
            return $this->successResponse(200, 'Token succesfully inserted', $pass_token);
        }
        return $this->errorResponse(404, 'User with that email not found');
    }

    public function resetUserPasswordToken()
    {
        if (!$this->request->has('email')) {
            return $this->missingFieldResponse('email');
        }

        if ($this->userService->resetUserPasswordUpdateToken($this->request->email)) {
            return $this->successResponse(200, 'Token succesfully removed');
        }
        return $this->errorResponse(404, 'User with that email not found');
    }

    public function removeUser(int $id)
    {
        if ($this->userService->deleteUser($id)) {
            return $this->successResponse(200, 'User succesfully deleted');
        }
        return $this->errorResponse(404, 'No user found with that ID');
    }
}
