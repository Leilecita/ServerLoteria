<?php

require_once "BaseController.php";
require_once __DIR__.'/../models/TicketModel.php';

class TicketsController extends BaseController {

    function __construct(){
        parent::__construct();
        $this->model = new TicketModel();
    }


    function logEditionEvent($previous,$updated){

       // $this->logEvent($this->getModel()->getLogName($updated),$this->getModel()->getStateEdited() ,$this->getModel()->getAmount($updated),$this->change($previous, $updated));
        $this->logEvent($this->getModel()->getLogName($updated),$this->getModel()->getStateEdited() ,$previous['name'],$this->change($previous, $updated));
    }

    function change($previous, $updated){

        $name="";
        $value="";
        $stock="";
        $sold="";

        if($previous['name'] !== $updated['name']){
           $name= "Nombre: ".$previous['name']. " -> ". $updated['name']." ";
        }
        if($previous['amount'] !== $updated['amount']){
            $value= "Precio: ".$previous['amount']. " -> ". $updated['amount']." ";
        }
        if($previous['stock'] !== $updated['stock']){
            $stock="Stock: ".$previous['stock']. " -> ". $updated['stock']." ";
        }
        if($previous['sold_quantity'] !== $updated['sold_quantity']){
            $sold="Cantidad vendida: ".$previous['sold_quantity']. " -> ". $updated['sold_quantity'];
        }

        return '['.$this->getModel()->getStateEdited().'] '.$name.$value.$stock.$sold;

    }


}