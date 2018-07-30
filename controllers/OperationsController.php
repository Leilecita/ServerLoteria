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
            $this->updateDebtUser($data);
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

    function post(){

        parent::post();

        $data = (array) json_decode(file_get_contents("php://input"));
        $this->updateDebtUser($data);
    }

    function delete(){

        $operation=$this->getModel()->findById($_GET["id"]);

        parent::delete();

        $this->updateDebtUser($operation);

    }

    function updateDebtUser($data){
        if($this->users->findById($data["user_id"])){
            $totalAmount= $this->getModel()->getSumAmountByUserId($data["user_id"]);
            $this->users->updateDebtUser($data["user_id"],array('debt'=> $totalAmount));
        }
    }


    function logEditionEvent($previous,$updated){

       // $this->logEvent($this->getModel()->getLogName($updated),$this->getModel()->getStateEdited() ,$this->getModel()->getAmount($updated),$this->change($previous, $updated));
        $this->logEvent($this->getModel()->getLogName($updated),$previous['user_name'] ,$this->getModel()->getAmount($updated),$this->change($previous, $updated));
    }

    function change($previous, $updated){

        $value="";
        $observation="";
        $userName="";
        $created="";

        if($previous['amount'] !== $updated['amount']){
            $value= "Valor: ".$previous['amount']. " -> ". $updated['amount']." ";
        }
        if($previous['observation'] !== $updated['observation']){
            $observation= "Observación: ".$previous['observation']. " -> ". $updated['observation']." ";
        }
        if($previous['user_name'] !== $updated['user_name']){
            $userName= "DNI/User: ".$previous['user_name']. " -> ". $updated['user_name']." ";
        }
        if($previous['created'] !== $updated['created']){
            $created= "Fecha: ".$previous['created']. " -> ". $updated['created'];
        }

        return '['.$this->getModel()->getStateEdited().'] '.$value.$observation.$userName.$created;

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