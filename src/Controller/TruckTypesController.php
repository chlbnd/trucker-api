<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Service\TruckTypeService;
use App\Helper\RequestSplitter;

class TruckTypesController extends BaseController
{
    /**
     * @var TruckerRepository
     */
    private $truckerRepository;

    public function __construct(
        TruckTypeService $service,
        RequestSplitter $requestSplitter
    ) {
        parent::__construct($service, $requestSplitter);
    }
}
