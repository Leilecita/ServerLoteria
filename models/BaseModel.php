<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 10/5/18
 * Time: 15:35
 */

include __DIR__ . '/../config/config.php';
require __DIR__ . '/../libs/dbhelper.php';
use vielhuber\dbhelper\dbhelper;

abstract class BaseModel{
    protected $tableName  = '';
    private $db;

    function __construct(){
        global $DBCONFIG;
        $this->db = new dbhelper();
        //$this->db->connect('pdo', 'mysql', '127.0.0.1', 'root', null, 'loteria', 3306);
        $this->db->connect('pdo', 'mysql', $DBCONFIG['HOST'], $DBCONFIG['USERNAME'], $DBCONFIG['PASSWORD'],$DBCONFIG['DATABASE'],$DBCONFIG['PORT']);
    }

    public function getLogName($data){
        return get_class($this);
    }


    public function getDescription($data){
        return "";
    }

    public function getDescriptionDeletion($data){
        return "";
    }

    public function getDescriptionEdition($previous,$edited){
        return "";
    }

    public function getState($data){
        return "Nuevo";
    }

    function getStateEdited(){
        return "Editado";
    }


    public function getAmount($data){
        return 0;
    }

    public function getDb(){
        return $this->db;
    }

    function count(){
        $response = $this->db->fetch_row('SELECT COUNT (id) AS total FROM '.$this->tableName);

        if($response["total"]!=null){
            return $response["total"];
        }else{
            $response["total"]=0;
            return $response;
        }
    }

    function sum($date){
        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName.' WHERE created >= ? ORDER BY created ASC',$date);
        if($response["total"]!=null){
            return $response["total"];
        }else{
            return 0;
        }
    }

    function getSumAmountByDate($date){
        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName.' WHERE created >= ?',$date);
        if($response['total'] != null){

            return $response;
        }else{
            $response['total']=0.0;
            return $response;
        }

    }

    function getSumAmount(){

        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName );
        if($response['total'] != null){
            return $response;

        }else{
            $response['total']=0.0;
            return $response;
        }

    }

    function getSumAmountByUserId($user_id){
        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName.' WHERE user_id = ?',$user_id);

        if($response["total"] != null){
            return $response;
        }else{
            $response["total"]=0.0;
            return $response;
        }

    }


    //sin sube
    function getSumAmountOnlyAwards($date){
        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName.' WHERE created >= ? AND observation = ?',$date, "");
        if($response['total'] != null){
            return $response;
        }else{
            $response['total']=0.0;
            return $response;
        }

    }

    function sumPreimpresos(){

        $list=$this->db->fetch_all('SELECT * FROM '.$this->tableName);
        $totalAmount=0;
        for($i = 0; $i < count($list); ++$i) {

            $totalAmount=$totalAmount+($list[$i]['amount']*$list[$i]['sold_quantity']);
        }

        return $totalAmount;
    }

    function sumSube($date,$obs){
        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName.' WHERE created >= ? AND observation = ?',$date,$obs);
        if($response['total'] != null){
            return $response["total"];
        }else{

            return 0;
        }

    }

    function sumAwards($date){
        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName.' WHERE created >= ? AND observation = ?',$date,"");
        if($response['total'] != null){
            return $response["total"];
        }else{

            return 0;
        }

    }

    function sumSoldMistakes(){
        $response = $this->db->fetch_row('SELECT SUM(amount) AS total FROM '.$this->tableName.' WHERE  sold_today = ? ORDER BY created DESC',"true");
        if($response["total"] != null){
            return $response["total"]*(-1);
        }else{
            return 0;
        }
    }

    function findLast(){
        return $this->db->fetch_row('SELECT * FROM '.$this->tableName.' ORDER BY id DESC LIMIT 1');
    }

    function findById($id){
        return $this->db->fetch_row('SELECT * FROM '.$this->tableName.' WHERE id = ?',$id);
    }
    function findByUserId($user_id){
        return $this->db->fetch_row('SELECT * FROM '.$this->tableName.' WHERE document = ?',$user_id);
    }

    function findAll($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }

    function findAllByDate($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC';

        return $this->db->fetch_all($query);
    }

    function findAllByName($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY name ASC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }
    function save($data){
        return $this->db->insert($this->tableName, $data );
    }

    function update($id, $data){
       return  $this->db->update($this->tableName, $data,['id' => "$id"]);
    }

    function updateMistakes($sold, $data){
        return  $this->db->update($this->tableName, $data,['sold_today' => "$sold"]);
    }

    function updateAll( $data){
        $query = 'UPDATE '.$this->tableName.' SET ';
        foreach ($data as $k => $v){
            $query .= " $k = $v";
        }
        return  $this->db->query($query);
    }

    function delete($id){
        return ($this->db->delete($this->tableName, ['id' => $id]) == 1);
    }



    function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        fwrite( $ifp, base64_decode( $base64_string ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }

}