<?php

require_once "BaseController.php";
require_once __DIR__.'/../models/TicketModel.php';

class TicketsController extends BaseController {

    function __construct(){
        parent::__construct();
        $this->model = new TicketModel();
    }

}