<?php

require_once "BaseController.php";
require_once __DIR__.'/../models/EventModel.php';

class EventsController extends BaseController {

    function __construct() {
        parent::__construct();
        $this->model = new EventModel();
    }




}