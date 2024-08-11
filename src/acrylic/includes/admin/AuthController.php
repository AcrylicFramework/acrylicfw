<?php

namespace App\Includes\Admin;

use App\Includes\Controller;
use App\Includes\Logger;
use App\Includes\Request;
use App\Includes\Response;
use App\Includes\Auth;

class AuthController extends Controller {

    public function login(): Response {
        if (Auth::check()) {
            return $this->redirect('/');
        }
        return $this->view('login', ['error' => 'none']);
    }

    public function login_post(): Response {
        $username = $this->request->input('user');
        $password = $this->request->input('password');

        if (Auth::check()) {
            return $this->redirect('/');
        }

        if (Auth::attempt($username, $password)) {
            return $this->redirect('/');
        }

        return $this->view('login', ['error' => 'User or Password invaild']);
    }

    public function logout(): Response {
        Auth::logout();
        return $this->redirect('/login');
    }
}