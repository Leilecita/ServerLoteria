<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 17/5/18
 * Time: 18:33
 */

require_once 'BaseModel.php';
class EventModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'events';
    }


}