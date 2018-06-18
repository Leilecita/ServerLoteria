<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 10/5/18
 * Time: 15:49
 */
require_once 'BaseModel.php';

class UserModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'users';
    }


    function save($data){
        error_log(print_r($data,true));
        if(empty($data['imageData'])) {
            unset($data['imageData']);
            return $this->getDb()->insert($this->tableName, $data);
        }else{
            $filePath = '/uploads/users/'.time().'.jpg';
            $this->base64_to_jpeg($data['imageData'],__DIR__.'/..'.$filePath);
            unset($data['imageData']);
            $data['image_url'] = $filePath;

            return $this->getDb()->insert($this->tableName, $data);
        }
    }

    public function getLogName($data){
        return "Usuario";
    }

    public function getState($data){
        return $data['name'];
    }

    public function getDescription($data){
        return "Nuevo";
    }

    public function getDescriptionDeletion($data){
        return $data['name']." ".$data['phone']." ".$data['document'];
    }

}