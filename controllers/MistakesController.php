<?php

require_once "BaseController.php";
require_once __DIR__.'/../models/MistakeModel.php';

class MistakesController extends BaseController {

    function __construct(){
        parent::__construct();
        $this->model = new MistakeModel();
    }

    function amount(){
        $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

        $totalAmount= $this->getModel()->getSumAmountBydate($created);

        return $this->returnSuccess(200,$totalAmount);
    }

    function logEditionEvent($previous,$updated){

       // $this->logEvent($this->getModel()->getLogName($updated),$this->getModel()->getStateEdited() ,$this->getModel()->getAmount($updated),"[Vendido] ".$this->getModel()->getDescriptionDeletion($updated));
        $this->logEvent($this->getModel()->getLogName($updated),$previous['name'] ,$this->getModel()->getAmount($updated),"[Vendido] ".$this->getModel()->getDescriptionDeletion($updated));
    }



}