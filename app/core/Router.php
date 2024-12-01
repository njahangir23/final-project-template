<?php

namespace app\core;

use app\controllers\MainController;
use app\controllers\UserController;
use app\controllers\RecommendationController;

class Router {
    public $urlArray;

    function __construct()
    {
        $this->urlArray = $this->routeSplit();
        $this->handleMainRoutes();
        $this->handleUserRoutes();
        $this->handleRecommendationRoutes();
    }

    protected function routeSplit() {
        $removeQueryParams = strtok($_SERVER["REQUEST_URI"], '?');
        return explode("/", $removeQueryParams);
    }

    protected function handleMainRoutes() {
        if ($this->urlArray[1] === '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->homepage();
        }
    }

    protected function handleUserRoutes() {
        if ($this->urlArray[1] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController = new UserController();
            $userController->usersView();
        }

        //give json/API requests a api prefix
        if ($this->urlArray[1] === 'api' && $this->urlArray[2] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController = new UserController();
            $userController->getUsers();
        }

        if ($this->urlArray[1] === 'users' && $this->urlArray[2] === 'view' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController = new UserController();
            $userController->userView();
        }
    
    }

    protected function handleRecommendationRoutes() {
        if($this->urlArray[1] === 'recommendations' && $_SERVER['REQUEST_METHOD'] === 'GET' && !isset($this->urlArray[2])) {
            $recommendationController = new RecommendationController();
            $recommendationController->recommendationView();
        }

        if($this->urlArray[1] === 'api' && $this->urlArray[2] === 'recommendations' && $_SERVER['REQUEST_METHOD'] === 'GET'){
            if(isset($_GET['artist']) && !empty($_GET['artist'])) {
                $artist = $_GET['artist'];
                $recommendationController = new RecommendationController();
                $recommendationController->getRecommendations($artist);
            } else {
                header("Content-Type: application/json");
                echo json_encode([
                    'error' => true,
                    'message' => "Artist name is required."
                ]);
                exit();
            }
        }
    }

 }