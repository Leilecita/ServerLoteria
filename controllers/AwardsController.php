<?php

require_once "BaseController.php";
require_once __DIR__.'/../models/AwardModel.php';

class AwardsController  extends BaseController {

    function __construct() {
        parent::__construct();
        $this->model = new AwardModel();
    }

    function amount(){
        $created = isset($_GET['created']) ? $_GET['created'] : '1900-01-01';

        $totalAmount= $this->getModel()->getSumAmountOnlyAwards($created);

        return $this->returnSuccess(200,$totalAmount);
    }

}