<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 4/6/18
 * Time: 18:37
 */

class ReportsFileModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'reports_file';
    }


}