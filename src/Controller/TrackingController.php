<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Helper\RequestSplitter;
use App\Repository\TruckerRepository;
use App\Service\TrackingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackingController extends BaseController
{
    /**
     * @var TrackingService
     */
    private $service;
    /**
     * @var array
     */
    private $requestSplitter;
    /**
     * @var TruckerRepository
     */
    private $truckerRepository;

    public function __construct(
        TrackingService $service,
        RequestSplitter $requestSplitter
    ) {
        $this->service = $service;
        $this->requestSplitter = $requestSplitter;
        parent::__construct($this->service, $this->requestSplitter);
    }

    public function getRecent(Request $request): Response
    {
        $queryString = $this->requestSplitter->splitData($request);
        $params = $this->service->daysToDateRange($queryString);

        try {
            $entityList = $this->service->listCheckInByDateRange($params);
            $response = $this->getSuccessResponse($entityList);

            return $response->getResponse();
        } catch(\Exception $e) {
            $response = $this->getFailResponse($e);
            return $response->getResponse();
        }
    }

    public function getByCheckIn(Request $request): Response
    {
        $params = $this->requestSplitter->splitData($request);

        try {
            $entityList = $this->service->listCheckInByDateRange($params);
            $response = $this->getSuccessResponse($entityList);

            return $response->getResponse();
        } catch(\Exception $e) {
            $response = $this->getFailResponse($e);
            return $response->getResponse();
        }
    }

    public function getByTruckType(Request $request): Response
    {
        try {
            $entityList = $this->service->listAddressesByTruckType();
            $response = $this->getSuccessResponse($entityList);

            return $response->getResponse();
        } catch(\Exception $e) {
            $response = $this->getFailResponse($e);
            return $response->getResponse();
        }
    }
}
