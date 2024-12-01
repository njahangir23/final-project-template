<?php

namespace app\controllers;

abstract class Controller {

    public function returnView($pathToView) {
        require $pathToView;
        exit();
    }

    public function returnJSON($data) {
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }


}