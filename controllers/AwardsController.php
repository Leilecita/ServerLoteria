<?php

require_once "BaseController.php";
require_once __DIR__.'/../models/AwardModel.php';
require_once __DIR__.'/../models/TicketModel.php';

class AwardsController  extends BaseController {
    private $tickets;

    function __construct() {
        parent::__construct();
        $this->model = new AwardModel();
        $this->tickets= new TicketModel();
    }

    function amount(){
        $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

        $totalAmount= $this->getModel()->getSumAmountOnlyAwards($created);

        return $this->returnSuccess(200,$totalAmount);
    }

    function amountsube(){
        $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

        $totalAmount= $this->getModel()->getSumSube($created,"sube");

        return $this->returnSuccess(200,$totalAmount);
    }

    function post(){

        parent::post();
        $data = (array) json_decode(file_get_contents("php://input"));
        $this->updateDayAwardsTicket($data);

    }

    function delete(){

        $award=$this->getModel()->findById($_GET["id"]);

        $this->deleteDayAwardTicket($award,$award['amount']);
        parent::delete();

    }


    function deleteDayAwardTicket($data,$value){
        $ticket=$this->tickets->findByName($data["name"]);

        if($ticket){
            $total_amount=$ticket['day_awards'] - $value;

            $this->tickets->updateTicketsAwards($data["name"],array('day_awards'=> $total_amount));
        }

    }

    function updateDayAwardsTicket($data){

        $ticket=$this->tickets->findByName($data["name"]);

        if($ticket){
            $total_amount=$ticket['day_awards'] + $data['amount'];
            $this->tickets->updateTicketsAwards($data["name"],array('day_awards'=> $total_amount));
        }
    }


}