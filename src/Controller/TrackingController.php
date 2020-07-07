<?php

namespace App\Controller;

use App\Helper\RequestSplitter;
use App\Controller\BaseController;
use App\Service\TrackingService;
use App\Repository\TruckerRepository;

class TrackingController extends BaseController
{
    /**
     * @var TruckerRepository
     */
    private $truckerRepository;

    public function __construct(
        TrackingService $service,
        RequestSplitter $requestSplitter
    ) {
        parent::__construct($service, $requestSplitter);
    }
}
