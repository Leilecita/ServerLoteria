<?php
require_once 'BaseModel.php';
class TicketModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'tickets';
    }

    public function getLogName($data){
        return "Preimpreso";
    }

    public function getAmount($data){
        return $data['amount'];
    }

    public function getDescription($data){
        return "Nuevo";

    }

    public function getState($data){
        return $data['name'];
    }


    public function getDescriptionDeletion($data){
        return $data['name']." $".$data['amount']." Stock:".$data['stock']." Vendidos:".$data['sold_quantity'];
    }

}