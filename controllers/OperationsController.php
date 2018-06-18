<?php
require_once "BaseController.php";
require_once __DIR__.'/../models/UserModel.php';
require_once __DIR__.'/../models/OperationModel.php';

class OperationsController extends BaseController {

    private $users;


    function __construct(){
        parent::__construct();
        $this->model = new OperationModel();
        $this->users = new UserModel();
    }

    function put(){
        $data = (array) json_decode(file_get_contents("php://input"));
        if($this->users->findById($data["user_id"])){
            parent::put();
        }else{
            $this->returnError(404,'Usuario no existe');
        }
    }


    function amount(){

        if( isset($_GET['user_id'])){
            $totalAmount= $this->getModel()->getSumAmountByUserId($_GET['user_id']);
            $this->returnSuccess(200,$totalAmount);
        }else if( isset($_GET['created'])){
            $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

            $totalAmount= $this->getModel()->getSumAmountByDate($created);
            return $this->returnSuccess(200,$totalAmount);
        }else{
            $totalAmount= $this->getModel()->getSumAmount();
            return $this->returnSuccess(200,$totalAmount);
        }

    }

    /*
     *
     *  function amount(){

        if( isset($_GET['user_id'])){
            $totalAmount= $this->getModel()->getSumAmountByUserId($_GET['user_id']);
            $this->returnSuccess(200,$totalAmount);
        }else if( isset($_GET['created'])){
            $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

            $totalAmount= $this->getModel()->getSumAmountByDate($created);
            return $this->returnSuccess(200,$totalAmount);
        }else{
            $totalAmount= $this->getModel()->getSumAmount();
            return $this->returnSuccess(200,$totalAmount);
        }

    }
*/


}