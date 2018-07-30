<?php

require_once "BaseController.php";
require_once "ReportsFileController.php";
require_once "ReportsController.php";
require_once __DIR__.'/../models/MoneyBoxModel.php';
require_once __DIR__.'/../models/TicketModel.php';
require_once __DIR__.'/../models/MistakeModel.php';
require_once __DIR__.'/../models/ReportsFileModel.php';

class MoneyBoxController extends BaseController {

    function __construct() {
        parent::__construct();
        $this->model = new MoneyBoxModel();
    }

    function last(){
        $last = $this->getModel()->findLast();
        if(!empty($last)){
            $this->returnSuccess(200,$last);
        }else{

            $dataBox = array(
                array(
                    'debt_A' => "0.0",
                    'deposit' => "0.0",
                    'money_day_after' => "0.0",
                    'money_init_day' => "0.0"
                )
            );
            $res=$this->getModel()->save($dataBox);
            if($res<0){
                $this->returnError(404,null);
            }else{
                $inserted = $this->getModel()->findById($res);
                $this->returnSuccess(201,$inserted);
            }

        }

    }
    function save_report(){

        $reports=new ReportsController();
        $reportsFile=new ReportsFileController();

        $last = $this->getModel()->findLast();

        $dataReport = array(
            array(
                'awards' => "1.0", 'mistakes_day' => "0.0", 'mistakes_sold' => "0.0", 'sube' => "0.0",
                'operations' => "0.0",
                'tickets' => "0.0",
                'deposit' => "0.0",'debt_a' => "0.0",'box_moves' => "0.0",'init_day' => "0.0",'rest_box' => "0.0",'total_amount' => "0"
            )
        );



        if(empty($last)){
            $reportsFile->getModel()->save($dataReport);
        }else{

            $listReports=$reports->total($last['created']);
            $reportsFile->getModel()->save($listReports);
        }

    }
    public function post()
    {
        $this->save_report();

        $ticketModel = new TicketModel();
        $ticketModel->updateAll(array('sold_quantity' => 0));

        $mistakeModel= new MistakeModel();
        $mistakeModel->updateMistakes("true",array('observation' => 'vendido', 'sold_today' => 'false'));

        parent::post();
    }

    function logEditionEvent($previous,$updated){

        $this->logEvent($this->getModel()->getLogName($updated),$this->getModel()->getStateEdited() ,$this->getModel()->getAmount($updated),$this->change($previous, $updated));
    }

    function change($previous, $updated){

        if($previous['debt_a'] !== $updated['debt_a']){
            return "Deuda máquina A: ".$previous['debt_a']. " -> ". $updated['debt_a'];
        }else  if($previous['deposit'] !== $updated['deposit']){
            return "Depósito bancario: ".$previous['deposit']. " -> ". $updated['deposit'];
        }else  if($previous['money_day_after'] !== $updated['money_day_after']){
            return "Resto de caja: ".$previous['money_day_after']. " -> ". $updated['money_day_after'];
        }else  if($previous['money_init_day'] !== $updated['money_init_day']){
            return "Inicio del día: ".$previous['money_init_day']. " -> ". $updated['money_init_day'];
        }else{
            return "";
        }

    }
}