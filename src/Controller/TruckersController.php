<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Service\TruckerService;
use App\Helper\RequestSplitter;

class TruckersController extends BaseController
{
    public function __construct(
        TruckerService $service,
        RequestSplitter $requestSplitter
    ) {
        parent::__construct($service, $requestSplitter);
    }
}
