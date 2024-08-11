<?php

namespace App;

# modules
require_once 'includes/Config.php';
require_once 'includes/Logger.php';
require_once 'includes/Auth.php';
require_once 'includes/Param.php';
require_once 'includes/Session.php';
require_once 'includes/Request.php';
require_once 'includes/Response.php';
require_once 'includes/Template.php';
require_once 'includes/Controller.php';
require_once 'includes/Route.php';

use App\Includes\Config;
use App\Includes\Session;
use App\Includes\Response;
use App\Includes\Request;
use App\Includes\Route;
use App\Includes\Logger;

Config::$appDir = __DIR__;

try {    
    # Initialize session
    Session::start();

    # Define All Controllers
    $files = glob('includes/admin/*.php');
    foreach ($files as $file) {
        require_once $file;
    }

    # Define routes
    Route::get('/login', \App\Includes\Admin\AuthController::class, 'login');
    Route::post('/login', \App\Includes\Admin\AuthController::class, 'login_post');
    Route::get('/logout', \App\Includes\Admin\AuthController::class, 'logout');

    Route::get('/', \App\Includes\Admin\HomeController::class, 'index');

    # Render
    $request = new Request();
    $route = new Route($request);
    $target = $route->target();
    $target->render();

} catch (\Throwable $th) {
    Logger::error($th->getMessage());
    $err = new Response();
    $err->error(500);
    $err->render();
}
