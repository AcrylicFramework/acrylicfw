<?php

namespace App\Includes\Admin;

use App\Includes\Auth;
use App\Includes\Controller;
use App\Includes\Logger;
use App\Includes\Request;
use App\Includes\Response;

class HomeController extends Controller {

    public function index(): Response {

        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        $hello = 'Home';
        return $this->view('index', ['hello' => $hello]);
    }
}