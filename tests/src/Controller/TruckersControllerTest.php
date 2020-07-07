<?php

namespace App\Tests\Controller;

use App\Controller\TruckersController;
use PHPUnit\Framework\TestCase;

class TruckersControllerTest extends TestCase
{
    public function testInsertSuccess()
    {
        $trucker = '{
            "name": "Teste",
            "birthdate": "2000-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "AE",
            "is_loaded": false,
            "truck_type_id": 1
        }';

        $request = $this->createMock(
            'Symfony\Component\HttpFoundation\Request'
        );
        $request
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($trucker);

        $entity = $this->createMock('App\Entity\Trucker');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $factory = $this->createMock('App\Helper\TruckerFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($trucker)
            ->willReturn($entity);

        $cacheItem = $this->createMock('Psr\Cache\CacheItemInterface');

        $cachePool = $this->createMock('Psr\Cache\CacheItemPoolInterface');
        $cachePool
            ->expects($this->once())
            ->method('getItem')
            ->with('trucker_1')
            ->willReturn($cacheItem);

        $entityManager       = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository          = $this
            ->createMock('App\Repository\TruckerRepository');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $requestSplitter     = $this
            ->createMock('App\Helper\RequestSplitter');

        $controller = new TruckersController(
            $entityManager,
            $repository,
            $factory,
            $truckTypeRepository,
            $requestSplitter,
            $cachePool
        );
        $actual = json_decode($controller->insert($trucker));
        dump($actual);
    }
}