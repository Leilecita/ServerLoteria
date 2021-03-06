<?php
require_once 'BaseModel.php';
class MoneyBoxModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'money_box';
    }

    public function getLogName($data){
        return "Caja";
    }

    public function getAmount($data){
        return $data['money_init_day'];
    }

    public function getDescription($data){

       /* $datetime = new DateTime('2008-08-03 12:35:23');
        echo $datetime->format('Y-m-d H:i:s') . "\n";
        $la_time = new DateTimeZone('America/Los_Angeles');
        $datetime->setTimezone($la_time);
        echo $datetime->format('Y-m-d H:i:s');
*/

        $dtServer = new DateTime($data['created'], new DateTimeZone('UTC'));
        $timeZoneDefault = date_default_timezone_get();
        $dtServer->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));

        return $dtServer->format("Y-m-d H:i:s");
    }

    public function getDescriptionDeletion($data){

        $string="Inicio caja: ".$data['money_init_day']." Maq A: ".$data['debt_a']." Deposito: ".$data['deposit']." Resto de caja: ".$data['money_day_after'];

        return $string;
    }

    public function getState($data){
        return "";
    }
}