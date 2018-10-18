<?php

require_once "BaseController.php";
require_once __DIR__.'/../models/SpendingModel.php';

class SpendingsController extends BaseController {

    private $boxes;

    function __construct(){
        parent::__construct();
        $this->model = new SpendingModel();
        $this->boxes= new MoneyBoxModel();
    }




    function amount(){
        $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

        $totalAmount= $this->getModel()->getSumAmountByDate($created);

        return $this->returnSuccess(200,$totalAmount);
    }

   /* function post()
    {
        $data = (array) json_decode(file_get_contents("php://input"));

        if($data['name'] === "Comision"){

           $lastBox= $this->boxes->findLast();

           $this->boxes->update($lastBox["id"],array('comision'=>  $data['amount']));

        }
        parent::post();
    }*/
}