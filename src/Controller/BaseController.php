<?php

namespace App\Controller;

use App\Helper\RequestSplitter;
use App\Helper\ResponseFactory;
use App\Service\AbstractService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var AbstractService
     */
    private $service;
    /**
     * @var RequestSplitter
     */
    private $requestSplitter;

    public function __construct(
        AbstractService $service,
        RequestSplitter $requestSplitter
    ) {
        $this->service = $service;
        $this->requestSplitter = $requestSplitter;
    }

    public function insert(Request $request): Response
    {
        try{
            $entityData = $request->getContent();
            $entity = $this->service->createEntity($entityData);

            $response = $this->getSuccessResponse($entity);
        } catch (\Exception $e) {
            $response = $this->getFailResponse();
        }

        return $response->getResponse();
    }

    public function getOne(int $id): Response
    {
        $entity = $this->service->getEntity($id);

        if(is_null($entity)) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }

        $response = $this->getSuccessResponse($entity);

        return $response->getResponse();
    }

    public function getAll(Request $request): Response
    {
        $params = $this->requestSplitter->splitData($request);
        $offset = ($params['currentPage'] - 1) * $params['itemsPerPage'];

        $entityList = $this->service->getEntityList($params, $offset);

        $response = new ResponseFactory(
            $entityList,
            true,
            Response::HTTP_OK,
            $params['currentPage'],
            $params['itemsPerPage']
        );

        return $response->getResponse();
    }

    public function update(int $id, Request $request): Response
    {
        $updateData = $request->getContent();

        try {
            $updatedEntity = $this->service->entityUpdate($updateData, $id);

            $response = $this->getSuccessResponse($updatedEntity);
        } catch (\Exception $e){
            $response = $this->getFailResponse();
        }

        return $response->getResponse();
    }

    public function delete(int $id): Response
    {
        try {
            $this->service->deleteEntity($id);
        } catch (\Exception $e) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }
        return new Response("", Response::HTTP_NO_CONTENT);
    }

    private function getSuccessResponse($entity)
    {
        $response = new ResponseFactory(
            [
                $entity
            ],
            true,
            Response::HTTP_OK
        );

        return $response;
    }

    private function getFailResponse()
    {
        $response = new ResponseFactory(
            [
                "Check given info"
            ],
            false,
            Response::HTTP_BAD_REQUEST
        );

        return $response;
    }
}