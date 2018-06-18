<?php

require_once 'BaseModel.php';

class AwardModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'awards';
    }

    public function getLogName($data){
        return "Premio";
    }

    public function getDescription($data){
        return "Nuevo ";
    }
    public function getAmount($data){
        return $data['amount'];
    }


    public function getState($data){
        return $data['name'];
    }

    public function getDescriptionDeletion($data){
        return $data['name']." ".$data['amount'];
    }
}