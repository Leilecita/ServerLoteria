<?php

require_once 'BaseModel.php';

class MistakeModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'mistakes';
    }
    public function getLogName($data){
        return "Error";
    }

    public function getAmount($data){
        return $data['amount'];
    }

    public function getDescription($data){
        return "[Nuevo]";
    }

    public function getState($data){
        return $data['name'];
    }

    public function getStateEdited(){
        return "Vendido";
    }

    public function getDescriptionDeletion($data){
        return $data['information'];
    }


}