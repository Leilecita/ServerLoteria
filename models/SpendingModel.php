<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 17/5/18
 * Time: 18:13
 */
require_once 'BaseModel.php';

class SpendingModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'spendings';
    }
    public function getLogName($data){
        if($data['amount'] < 0){
            return "Egreso";
        }else{
            return "Ingreso";
        }
    }

    public function getAmount($data){
        return $data['amount'];
    }

    public function getState($data){
        return $data['description'];
    }

    public function getDescription($data){

        return "[Nuevo]";

    }

    public function getDescriptionDeletion($data){
        return $data['description'];
    }
}