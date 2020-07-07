<?php

namespace App\Tests\Service;

use App\Service\TruckerService;
use PHPUnit\Framework\TestCase;

class TruckerServiceTest extends TestCase
{
    /**
     * @covers TruckerService
     */
    public function testCreateEntitySuccess()
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

        $entity = $this
            ->createMock('App\Entity\Trucker');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($trucker)
            ->willReturn($entity);

        $cacheItem = $this
            ->createMock('Psr\Cache\CacheItemInterface');
        $cacheItem
            ->expects($this->once())
            ->method('set')
            ->with($entity);

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->with('trucker_1')
            ->willReturn($cacheItem);
        $cache
            ->expects($this->once())
            ->method('save');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $actual = $service->createEntity($trucker);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckerService
     * @expectedException \Exception
     */
    public function testCreateEntityFailure()
    {
        $trucker = '{"invalid": "data"}';

        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($trucker)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $service->createEntity($trucker);
    }

    /**
     * @covers TruckerService
     */
    public function testGetEntityCachedSuccess()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $cacheItem = $this
            ->createMock('Psr\Cache\CacheItemInterface');
        $cacheItem
            ->expects($this->once())
            ->method('get')
            ->willReturn($entity);

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->with('trucker_1')
            ->willReturn(true);
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->with('trucker_1')
            ->willReturn($cacheItem);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $actual = $service->getEntity($id);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckerService
     */
    public function testGetEntityNotCachedSuccess()
    {
        $id = 1;

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->with('trucker_1')
            ->willReturn(false);

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $actual = $service->getEntity($id);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckerService
     */
    public function testGetEntityFailure()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->with('trucker_1')
            ->willReturn(false);

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $actual = $service->getEntity($id);
        $expected = null;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckerService
     */
    public function testGetEntityListSuccess()
    {
        $params = [
            'filters'      => [],
            'sort'         => [],
            'itemsPerPage' => []
        ];
        $offset = 1;

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [], [], 1)
            ->willReturn([$entity]);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $actual = $service->getEntityList($params, $offset);
        $expected = [$entity];

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckerService
     */
    public function testGetEmptyEntityList()
    {
        $params = [
            'filters'      => [],
            'sort'         => [],
            'itemsPerPage' => []
        ];
        $offset = 1;

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [], [], 1)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $actual = $service->getEntityList($params, $offset);
        $expected = null;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckerService
     */
    public function testEntityUpdateSuccess()
    {
        $id = 1;
        $newTrucker = '{
            "name": "Teste Novo",
            "birthdate": "2000-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "AE",
            "is_loaded": false,
            "truck_type_id": 1
        }';

        $truckTypeEntity = $this
            ->createMock('App\Entity\TruckType');
        $truckTypeEntity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $truckTypeRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($truckTypeEntity);

        $entity = $this
            ->createMock('App\Entity\Trucker');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $entity
            ->expects($this->exactly(2))
            ->method('getTruckType')
            ->willReturn($truckTypeEntity);
        $entity
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Teste Novo');
        $entity
            ->expects($this->once())
            ->method('getBirthdate')
            ->willReturn("2000-01-01");
        $entity
            ->expects($this->once())
            ->method('getGender')
            ->willReturn("O");
        $entity
            ->expects($this->once())
            ->method('getIsOwner')
            ->willReturn(true);
        $entity
            ->expects($this->once())
            ->method('getCnhType')
            ->willReturn("AE");
        $entity
            ->expects($this->once())
            ->method('getIsLoaded')
            ->willReturn(false);
        $entity
            ->expects($this->once())
            ->method('setName')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('setBirthdate')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('setGender')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('setIsOwner')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('setCnhType')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('setIsLoaded')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('setTruckType')
            ->willReturn($entity);

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($newTrucker)
            ->willReturn($entity);

        $cacheItem = $this
            ->createMock('Psr\Cache\CacheItemInterface');
        $cacheItem
            ->expects($this->once())
            ->method('set')
            ->with($entity);

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->with('trucker_1')
            ->willReturn($cacheItem);
        $cache
            ->expects($this->once())
            ->method('save');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $actual = $service->entityUpdate($newTrucker, 1);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckerService
     * @expectedException \Exception
     */
    public function testEntityUpdateUnavailableTruckType()
    {
        $id = 1;
        $newTrucker = '{
            "name": "Teste Novo",
            "birthdate": "2000-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "AE",
            "is_loaded": false,
            "truck_type_id": 5
        }';

        $entity = $this
            ->createMock('App\Entity\Trucker');
        $entity
            ->expects($this->once())
            ->method('getTruckType')
            ->willReturn(null);

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($newTrucker)
            ->willReturn($entity);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $service->entityUpdate($newTrucker, 1);
    }

    /**
     * @covers TruckerService
     * @expectedException \Exception
     */
    public function testEntityUpdateUnavailableEntity()
    {
        $id = 1;

        $newTrucker = '{
            "name": "Teste",
            "birthdate": "2000-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "AE",
            "is_loaded": false,
            "truck_type_id": 5
        }';

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $service->entityUpdate($newTrucker, 1);
    }

    /**
     * @covers TruckerService
     */
    public function testDeleteEntitySuccess()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('deleteItem')
            ->with('trucker_1');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $service->deleteEntity($id);
    }

    /**
     * @covers TruckerService
     * @expectedException \Exception
     */
    public function testAlreadyDeletedEntity()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Trucker');

        $repository = $this
            ->createMock('App\Repository\TruckerRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckerFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckTypeRepository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckerService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckTypeRepository,
            $trackingRepository
        );

        $service->deleteEntity($id);
    }
}