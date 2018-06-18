<?php
include 'controllers/OperationsController.php';


$controller = new OperationsController();

$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
	case 'GET':
        $controller->get();
		break;
	case 'POST':
        $controller->post();
		break;
	case 'PUT':
        $controller->put();
	    break;
    case 'DELETE':
        $controller->delete();
        break;
	default:
        $controller->returnError(400,'INVALID METHOD');
	 break;
}
