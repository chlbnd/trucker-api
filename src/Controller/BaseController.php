<?php

namespace App\Controller;

use App\Helper\EntityFactory;
use App\Helper\RequestSplitter;
use App\Helper\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var EntityFactory
     */
    private $factory;

    /**
     * @var RequestSplitter
     */
    private $requestSplitter;

    public function __construct(
        EntityManagerInterface $entityManager,
        ObjectRepository $repository,
        EntityFactory $factory,
        RequestSplitter $requestSplitter
    ) {
        $this->entityManager   = $entityManager;
        $this->repository      = $repository;
        $this->factory         = $factory;
        $this->requestSplitter = $requestSplitter;
    }

    public function insert(Request $request): Response
    {
        try{
            $entity = $this->factory->create($request->getContent());

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $responseFactory = new ResponseFactory(
                [$entity],
                true,
                Response::HTTP_OK
            );
        } catch (\InvalidArgumentException $e) {
            $responseFactory = new ResponseFactory(
                ["Check given info"],
                false,
                Response::HTTP_BAD_REQUEST
            );
        }

        return $responseFactory->getResponse();

    }
    public function getOne(int $id): Response
    {
        $entity = $this->repository->find($id);

        if(is_null($entity)) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }

        $responseFactory = new ResponseFactory(
            [$entity],
            true,
            Response::HTTP_OK
        );

        return $responseFactory->getResponse();

    }
    public function getAll(Request $request): Response
    {
        $params = $this->requestSplitter->splitData($request);
        $offset = ($params['currentPage'] - 1) * $params['itemsPerPage'];

        $entityList = $this->repository->findBy(
            $params['filters'],
            $params['sort'],
            $params['itemsPerPage'],
            $offset
        );

        $responseFactory = new ResponseFactory(
            $entityList,
            true,
            Response::HTTP_OK,
            $params['currentPage'],
            $params['itemsPerPage']
        );

        return $responseFactory->getResponse();

    }
    public function update(int $id, Request $request): Response
    {
        $entity = $this->repository->find($id);

        if(is_null($entity)) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }

        try {
            $newEntity = $this->factory->create($request->getContent());
            $updatedEntity = $this->updateEntity($entity, $newEntity);

            $this->entityManager->flush();

            $responseFactory = new ResponseFactory(
                [$entity],
                true,
                Response::HTTP_OK
            );
        } catch (\InvalidArgumentException $e){
            $responseFactory = new ResponseFactory(
                ["Check update info"],
                false,
                Response::HTTP_BAD_REQUEST
            );
        }

        return $responseFactory->getResponse();

    }
    public function delete(int $id): Response
    {
        $entity = $this->repository->find($id);

        if(is_null($entity)) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new Response("", Response::HTTP_NO_CONTENT);
    }
}