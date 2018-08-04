<?php
require_once "BaseController.php";
require_once __DIR__.'/../models/AwardModel.php';
require_once __DIR__.'/../models/MistakeModel.php';
require_once __DIR__.'/../models/SpendingModel.php';
require_once __DIR__.'/../models/TicketModel.php';
require_once __DIR__.'/../models/OperationModel.php';
require_once __DIR__.'/../models/MoneyBoxModel.php';
require_once __DIR__.'/../models/EventModel.php';

class ReportsController extends BaseController
{


    function __construct(){
        parent::__construct();
        $this->model = null;

    }

    public function get()
    {
        if(isset($_GET['method'])){
            $this->method();
        }else {
            $this->returnError(404, "INVALID METHOD FOR REPORTS");
        }
    }

    function total($created){

        $awards = new AwardModel();
        $mistakes = new MistakeModel();
        $spendings = new SpendingModel();
        $transactions = new OperationModel();
        $money_box= new MoneyBoxModel();

        //todo chequear si existe???
        $lastBox = $money_box->findLast();
        $tickets = new TicketModel();

        $sumAwards= $awards->sumAwards($created);
        $mistakesDay=$mistakes->sum($created);
        $mistakesSold=$mistakes->sumSoldMistakes();
        $sube=$awards->sumSube($created,"sube");

        $sumOperations=$transactions->sum($created);
        $sumOperationsNewTrust=$transactions->sumNegative($created,0); // fiados del dia

        $sumOperationsDebtCancel=$transactions->sumPositive($created,0); //fiados de dias anteriores cancelados


        $sumTickets=$tickets->sumPreimpresos();
        $sumDeposit=$lastBox['deposit'];
        $sumDebtA=$lastBox['debt_a'];
        $sumMoves=$spendings->sum($created);

        $sumMovesSpending=$spendings->sumNegative($created,0.0);
        $sumMovesEntry=$spendings->sumPositive($created,0.0);

        $sumInitDay=$lastBox['money_init_day'];
        $sumRestBox=$lastBox['money_day_after'];

        $sum= abs($sumRestBox) + abs($sumDeposit) + abs($sumOperationsNewTrust) +abs($mistakesDay) + abs($sumMovesSpending) + abs($sumAwards);

        $rest= abs($sumInitDay) + abs($sumOperationsDebtCancel) + abs($sumTickets) + abs($mistakesSold) + abs($sube) + $sumDebtA + abs($sumMovesEntry);

        $total_amount2= $sum - $rest;

        $reportItems2 = array(
            array(
                'awards' => $sumAwards, 'mistakes_day' => $mistakesDay , 'mistakes_sold' => $mistakesSold, 'sube' => $sube,
                'operations' => $sumOperationsNewTrust,
                'operations_payed' => $sumOperationsDebtCancel,
                'tickets' =>  $sumTickets,
                'deposit' => $sumDeposit ,'debt_a' =>$sumDebtA,'box_moves' => $sumMoves,'init_day' => $sumInitDay,'rest_box' => $sumRestBox,
                'total_amount' =>$total_amount2,
                'created' => $lastBox['created']
            )
        );

        return $reportItems2;
    }


    function totales()
    {
        $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

        $awards = new AwardModel();
        $mistakes = new MistakeModel();
        $spendings = new SpendingModel();
        $transactions = new OperationModel();
        $money_box= new MoneyBoxModel();
        $lastBox = $money_box->findLast();
        $tickets = new TicketModel();

        $sumOperationsNewTrust=$transactions->sumNegative($created,0); // fiados del dia

        $sumOperationsDebtCancel=$transactions->sumPositive($created,0); //fiados de dias anteriores cancelados

        $mistakesDay=$mistakes->sum($created);
        $soldedMistakes=$mistakes->sumSoldMistakes();
        $sumMoves=$spendings->sum($created);

        $sumMovesSpending=$spendings->sumNegative($created,0.0);
        $sumMovesEntry=$spendings->sumPositive($created,0.0);

        $sumAwards= $awards->sumAwards($created);
        $sumTickets=$tickets->sumPreimpresos();
        $sube=$awards->sumSube($created,"sube");

        $reportItems = array();
        $reportItems[] = array('model_name'=> 'Premios pagados preimpresos', 'total'=>$sumAwards);
        $reportItems[] = array('model_name'=> 'Carga sube', 'total'=>$sube);
        $reportItems[] = array('model_name'=> 'Errores', 'total'=>$mistakesDay);

        $reportItems[] = array('model_name'=> 'Errores vendidos', 'total'=>$soldedMistakes);
        $reportItems[] = array('model_name'=> 'Movimientos de caja', 'total'=>$sumMoves);
        $reportItems[] = array('model_name'=> 'Preimpresos', 'total'=>$sumTickets);
        $reportItems[] = array('model_name'=> 'Fiados del día', 'total'=>$sumOperationsNewTrust);

        $reportItems[] = array('model_name'=> 'Pago de fiados anteriores', 'total'=>$sumOperationsDebtCancel);
        $reportItems[] = array('model_name'=> 'Depósito bancario', 'total'=>$lastBox['deposit']);
        $reportItems[] = array('model_name'=> 'Deuda maquina A', 'total'=>$lastBox['debt_a']);
        $reportItems[] = array('model_name'=> 'Resto de caja', 'total'=>$lastBox['money_day_after']);
        $reportItems[] = array('model_name'=> 'Inicio día', 'total'=>$lastBox['money_init_day']);


        $totalAmount=0;
        for($i = 0; $i < count($reportItems); ++$i) {
            $totalAmount=$totalAmount+$reportItems[$i]['total'] ;
        }

        $sum= abs($lastBox['money_day_after']) + abs($lastBox['deposit']) + abs($sumOperationsNewTrust) +abs($mistakesDay)  + abs($sumMovesSpending) + abs($sumAwards);

        $rest= abs($lastBox['money_init_day']) + abs($sumOperationsDebtCancel) + abs($soldedMistakes) + abs($sumTickets) + abs($sube) + $lastBox['debt_a'] + abs($sumMovesEntry);

        $totalAmount2= $sum - $rest;

        //$reportItems[] = array('model_name'=> 'TOTAL', 'total'=>$totalAmount);
        $reportItems[] = array('model_name'=> 'RESULTADO DE LA CAJA', 'total'=>$totalAmount2);

        $this->returnSuccess(200,array('name' => 'Report '.$created, 'items' => $reportItems));
    }

    function texto(){
        $awards = new AwardModel();
        $money_box= new MoneyBoxModel();
        $spendings = new SpendingModel();
        $transactions = new OperationModel();
        $tickets = new TicketModel();
        $mistakes = new MistakeModel();

        $textItem=array();

        $lastBox = $money_box->findLast();

        $created=$lastBox['created'];

        $sumDeposit=$lastBox['deposit'];
        $sumDebtA=$lastBox['debt_a'];
        $sumInitDay=$lastBox['money_init_day'];
        $sumRestBox=$lastBox['money_day_after'];

        $sumOperationsNewTrust=$transactions->sumNegative($created,0); // fiados del dia

        $sumOperationsDebtCancel=$transactions->sumPositive($created,0); //fiados de dias anteriores cancelados

        $mistakesDay=$mistakes->sum($created);
        $soldedMistakes=$mistakes->sumSoldMistakes();
        $sumMoves=$spendings->sum($created);

        $sumMovesSpending=$spendings->sumNegative($created,0.0);
        $sumMovesEntry=$spendings->sumPositive($created,0.0);

        $sumAwards= $awards->sumAwards($created);
        $sumTickets=$tickets->sumPreimpresos();
        $sube=$awards->sumSube($created,"sube");

        $sum= abs($sumRestBox) + abs($sumDeposit) + abs($sumOperationsNewTrust) +abs($mistakesDay) + abs($sumMovesSpending) + abs($sumAwards);

        $rest= abs($sumInitDay) + abs($sumOperationsDebtCancel) + abs($sumTickets) + abs($soldedMistakes) + abs($sube) + $sumDebtA + abs($sumMovesEntry);

        $total_amount= $sum - $rest;

        $textItem[]=" TOTALES: \n";
        $textItem[]="  Premios pagados: ".$sumAwards."\n";
        $textItem[]="  Movimientos de caja: ".$sumMoves."\n";

        $textItem[]="  Fiados de día: ".$sumOperationsNewTrust." \n";
        $textItem[]="  Pago de fiados anteriores: ".$sumOperationsDebtCancel." \n";

        $textItem[]="  Venta preimpresos: ".$sumTickets." \n";

        $textItem[]="  Errores del día: ".$mistakesDay." \n";
        $textItem[]="  Errores vendidos: ".$soldedMistakes." \n";

        $textItem[]="  Cargas sube: ".$sube."\n";

        $textItem[]=" CAJA \n";
        $textItem[]="  Depósito bancario: $".$lastBox['deposit']."\n";
        $textItem[]="  Deuda maquina A: $".$lastBox['debt_a']."\n";
        $textItem[]="  Resto de caja: $".$lastBox['money_day_after']."\n";
        $textItem[]="  Inicio del día: $".$lastBox['money_init_day']."\n";

        $textItem[]=" RESULTADO DE LA CAJA: ".$total_amount."\n";

        $this->returnSuccess(200,$textItem);

    }

   /* function texto(){

        $awards = new AwardModel();
        $money_box= new MoneyBoxModel();
        $spendings = new SpendingModel();
        $transactions = new OperationModel();
        $tickets = new TicketModel();
        $mistakes = new MistakeModel();
        $events = new EventModel();

        $textItem=array();

        $lastBox = $money_box->findLast();

        $filters=array();

        $ticketsList=$tickets->findAllByDate($filters);

        $filters[] = 'created >= "'.$lastBox['created'].'"';

        $eventsList=$events->findAllByDate($filters);
        $awardsList=$awards->findAllByDate($filters);
        $spendingsList=$spendings->findAllByDate($filters);
        $transactionsList=$transactions->findAllByDate($filters);
        $mistakesTodayList=$mistakes->findAllByDate($filters);

        $filters[]= 'observation = "sube"';
        $subeList=$awards->findAllByDate($filters);

        $filtersMistake[] = 'sold_today = "true"';
        $mistakesList=$mistakes->findAllByDate($filtersMistake);


        $textItem[]=" Premios: \n";
        for($i=0;$i < count($awardsList); $i++){
            $textItem[]= "* Premio pagado : ".$awardsList[$i]['name']. " - valor $".$awardsList[$i]['amount']." \n";
        }

        $textItem[]=" Movimientos: \n";
        for($i=0;$i < count($spendingsList); $i++){
            $textItem[]= "* Movimiento : ".$spendingsList[$i]['description']. " - valor $".$spendingsList[$i]['amount']." \n";
        }

        $textItem[]=" Fiados: \n";
        for($i=0;$i < count($transactionsList); $i++){
            $textItem[]= "* Fiado : ".$this->formatDate($transactionsList[$i]['created']). " - :".$transactionsList[$i]['user_name']. " - valor $".$transactionsList[$i]['amount']." \n";
        }

        $textItem[]=" Preimpresos:  \n";
        for($i=0;$i < count($ticketsList); $i++){
            $textItem[]= "* Preimpreso : ".$ticketsList[$i]['name']. " - valor $".$ticketsList[$i]['amount']. " - Vendidos ".$ticketsList[$i]['sold_quantity'].
                " = $".($ticketsList[$i]['sold_quantity']*$ticketsList[$i]['amount'])." \n";
        }

        $textItem[]=" Errores del día: \n";
        for($i=0;$i < count($mistakesTodayList); $i++){
            $textItem[]= "* Error nuevo : ".$this->formatDate($mistakesTodayList[$i]['created']). " - :".$mistakesTodayList[$i]['name']. " - valor $".$mistakesTodayList[$i]['amount']." \n";
        }

        $textItem[]=" Errores vendidos: \n";
        for($i=0;$i < count($mistakesList); $i++){
            $textItem[]= "* Error vendido con fecha : ".$this->formatDate($mistakesList[$i]['created']). " - :".$mistakesList[$i]['name']. " - valor $".$mistakesList[$i]['amount']." \n";
        }

        $textItem[]=" Cargas sube: \n";
        for($i=0;$i < count($subeList); $i++){
            $textItem[]= "* ".$subeList[$i]['name']. " - carga $".$subeList[$i]['amount']." \n";
        }

        $textItem[]=" Caja del día: \n";
        $textItem[]=" Depósito: $".$lastBox['deposit']."\n";
        $textItem[]=" Deuda maquina A: $".$lastBox['debt_a']."\n";
        $textItem[]=" Resto de caja: $".$lastBox['money_day_after']."\n";
        $textItem[]=" Inicio del día: $".$lastBox['money_init_day']."\n";

       $textItem[]=" Historial de eventos: \n";
        for($i=0;$i < count($eventsList); $i++){
            $textItem[]= "* ".$eventsList[$i]['name']. " - ".$eventsList[$i]['state']. " - ".$eventsList[$i]['amount'].
                " - ".$eventsList[$i]['observation']. " - ".$this->formatDate($eventsList[$i]['created'])." \n";
        }

        $this->returnSuccess(200,$textItem);

    }*/

    function formatDate($date){
        $dtServer = new DateTime($date, new DateTimeZone('UTC'));
        $timeZoneDefault = date_default_timezone_get();
        $dtServer->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));

        return $dtServer->format("Y-m-d H:i:s");
    }

}