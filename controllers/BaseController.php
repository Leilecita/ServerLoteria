<?php

require_once __DIR__.'/../models/EventModel.php';

date_default_timezone_set('UTC');
define('PAGE_SIZE',10);

class BaseController {

    protected $model;

    protected $eventModel;

    function __construct()
    {
        $this->eventModel = new EventModel();
    }

    function getModel(){
        return $this->model;
    }

    function getEventModel(){
        return $this->eventModel;
    }

    function getFilters(){
        $filters= array();
        if(isset($_GET['since'])){
            $filters[] = 'created >= "'.$_GET['since'].'"';
        }
        if(isset($_GET['to'])){
            $filters[] = 'created < "'.$_GET['to'].'"';
        }
        if(isset($_GET['observation'])){
            $filters[] = 'observation = "'.$_GET['observation'].'"';
        }
        if(isset($_GET['obs'])){
           // array_push($filters,'observation = "'.$_GET['obs'].'"');
            $filters[] = 'sold_today = "'.$_GET['obs'].'"';
        }
        if(isset($_GET['equalname'])){
            $filters[] = 'name = "'.$_GET['equalname'].'"';
        }
        if(isset($_GET['user_id'])){
            $filters[] = 'user_id = "'.$_GET['user_id'].'"';
        }
        if(isset($_GET['document'])){
            $filters[] = 'document = "'.$_GET['document'].'"';
        }
        if(isset($_GET['created_equal'])){
            $filters[] = 'created = "'.$_GET['created_equal'].'"';
        }
        return $filters;
    }

    function getPaginator(){
            $paginator = array('offset' => 0, 'limit' => PAGE_SIZE);
        if(isset($_GET['page'])){
            $paginator['offset'] = PAGE_SIZE * $_GET['page'];
           // $paginator['limit'] = PAGE_SIZE * $_GET['page'];
        }
        return $paginator;
    }

    function validateId(){
        return isset($_GET['id']);
    }

    function validateUserId(){
        return isset($_GET['user_id']);
    }

    function validateCreated(){
        return isset($_GET['created']);
    }


    private function returnJson($code, $data=null){
        http_response_code($code);
        header('Content-Type: application/json');
        if($data!=null){
            echo json_encode($data);
        }
    }

    function returnSuccess($code,$data){
        $this->returnJson($code,array('result'=>'success', 'data'=>$data));
    }


    function returnError($code,$message){
        $this->returnJson($code,array('result'=>'error', 'message'=>$message));
    }

    function returnCreated(){
        http_response_code(201);
        header('Content-Type: application/json');
    }


    function method(){
        if(method_exists($this,$_GET['method'])){
            $this->{$_GET['method']}();
        }else {
            $this->returnError(404, "CONTROLLER METHOD NOT FOUND");
        }
    }

    function get(){
        if(isset($_GET['method'])){
            $this->method();
        }else if($this->validateId()){
            $entity = $this->getModel()->findById($_GET['id']);
            if(!empty($entity)){
                $this->returnSuccess(200,$entity);
            }else{
                $this->returnError(404,"ENTITY NOT FOUND");
            }

        }else{
            $this->returnSuccess(200,$this->getModel()->findAll($this->getFilters(),$this->getPaginator()));
        }
    }


    function post(){
        $data = (array)json_decode(file_get_contents("php://input"));
        unset($data['id']);
        $res = $this->getModel()->save($data);
        if($res<0){
            $this->returnError(404,null);
        }else{
            $inserted = $this->getModel()->findById($res);

            $this->logCreationEvent($inserted);

            $this->returnSuccess(201,$inserted);
        }
    }

    function put(){
        $data = (array) json_decode(file_get_contents("php://input"));

        if(isset($data['id'])){

            $id = $data['id'];
            unset($data['id']);

            $object=$this->getModel()->findById($id);
            if($object){

                $this->getModel()->update($id,$data);

                $updated=$this->getModel()->findById($id);


                $this->logEditionEvent($object,$updated);

                $this->returnSuccess(200,$updated);
            }else{
                $this->getModel()->save($data);
                $this->returnSuccess(201,$data);
            }
        }else{
            $this->getModel()->save($data);
            $this->returnSuccess(201,$data);
        }

    }

    function delete(){
        $deleted=$this->getModel()->findById($_GET['id']);
        if($this->getModel()->delete($_GET['id'])){

            $this->logDeletionEvent($deleted);

            $this->returnSuccess(204,null);
        }else{
            $this->returnError(404,"ENTITY #".$_GET['id']." NOT FOUND");
        }
    }


    function logEvent($name,$state,$amount,$observation){
        $this->eventModel->save(array('name' => $name, 'state' => $state, 'amount' => $amount, 'observation'=> $observation));
    }

    function logEventClose($name,$state,$amount,$observation){
        $this->logEvent($name,$state,$amount,$observation);
    }

    function logCreationEvent($data){
        $this->logEvent($this->getModel()->getLogName($data),$this->getModel()->getState($data),$this->getModel()->getAmount($data),$this->getModel()->getDescription($data));
    }


   function logEditionEvent($previous,$updated){

       $this->logEvent($this->getModel()->getLogName($updated),$this->getModel()->getStateEdited() ,$this->getModel()->getAmount($updated),$this->getModel()->getDescriptionDeletion($updated));
       $this->logEvent($this->getModel()->getLogName($previous),"Antes",$this->getModel()->getAmount($previous),$this->getModel()->getDescriptionDeletion($previous));
   }


    function logDeletionEvent($data){
        $this->logEvent($this->getModel()->getLogName($data),"Eliminado",$this->getModel()->getAmount($data),$this->getModel()->getDescriptionDeletion($data));
    }


}