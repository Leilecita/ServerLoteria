<?php
include 'controllers/ReportsController.php';


$controller = new ReportsController();

$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'GET':
        $controller->get();
        break;
    default:
        $controller->returnError(400,'INVALID METHOD');
        break;
}