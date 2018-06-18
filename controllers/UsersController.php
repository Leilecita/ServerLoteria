<?php
require_once "BaseController.php";
require_once __DIR__.'/../models/UserModel.php';
require_once __DIR__.'/../models/OperationModel.php';

class UsersController extends BaseController {


    function __construct(){
        parent::__construct();
        $this->model = new UserModel();
    }

    public function delete()
    {
        $user_id = $_GET['id'];
        $user = $this->getModel()->findById($user_id);
        if(!empty($user)) {
            $operationModel = new OperationModel();
            $operationModel->deleteByUserId($user_id);
            parent::delete();
           // $this->logEvent('Usuario', 'Eliminado ' . $user_id, 0, $user['name']);
        } else{
            $this->returnError(404,"USER NOT FOUND");
        }
    }

    public function post(){


        parent::post();
    }
}