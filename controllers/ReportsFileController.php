<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 4/6/18
 * Time: 18:36
 */

require_once "BaseController.php";
require_once __DIR__.'/../models/ReportsFileModel.php';

class ReportsFileController extends BaseController {

    function __construct() {
        parent::__construct();
        $this->model = new ReportsFileModel();
    }

}