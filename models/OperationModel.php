<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 10/5/18
 * Time: 15:49
 */
require_once 'BaseModel.php';

class OperationModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'operations';
    }

    function findAllByUser($user_id){
        return $this->getDb()->fetch_all('SELECT * FROM '.$this->tableName.' WHERE user_id = ?',$user_id);
    }

    function deleteByUserId($user_id){
        return ($this->getDb()->delete($this->tableName, ['user_id' => $user_id]));
    }
    public function getLogName($data){
        return "Fiado";
    }

    public function getAmount($data){
        return $data['amount'];
    }

    public function getDescriptionDeletion($data){

        $user=$this->getDb()->fetch_row('SELECT * FROM users WHERE id = ?',$data['user_id']);

        return $user['name']." ".$data['amount'];
    }


    public function getDescription($data){
        $user=$this->getDb()->fetch_row('SELECT * FROM users WHERE id = ?',$data['user_id']);

        return $user['name'];

    }

    public function getState($data){
        if($data['amount'] <0){
            return " Deuda por";
        }else{
            return " A cuenta";
        }
    }
}