<?php

namespace Controllers;
use App\Router;

class HomeController {

    public static function index( Router $router ) {

        $router->render('home/index', [
            'inicio' => true
        ]);
    }
}