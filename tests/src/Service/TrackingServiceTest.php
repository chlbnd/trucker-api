<?php

namespace App\Tests\Service;

use App\Service\TrackingService;
use PHPUnit\Framework\TestCase;

class TrackingServiceTest extends TestCase
{
    /**
     * @covers TrackingService
     */
    public function testCreateEntitySuccess()
    {
        $tracking = '{
            "trucker_id": 1,
            "from": {
                "street_name": "Rua",
                "street_number": "100",
                "neighborhood": "Bairro",
                "zip_code": "01234567",
                "city": "Sao Paulo",
                "state": "SP"
            },
            "to": {
                "street_name": "Avenida",
                "street_number": "1000",
                "neighborhood": "Vila",
                "zip_code": "76543210",
                "city": "Manaus",
                "state": "AM"
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02"
        }';

        $entity = $this
            ->createMock('App\Entity\Tracking');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($tracking)
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
            ->with('tracking_1')
            ->willReturn($cacheItem);
        $cache
            ->expects($this->once())
            ->method('save');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $actual = $service->createEntity($tracking);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TrackingService
     * @expectedException \Exception
     */
    public function testCreateEntityFailure()
    {
        $tracking = '{"invalid": "data"}';

        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($tracking)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $service->createEntity($tracking);
    }

    /**
     * @covers TrackingService
     */
    public function testGetEntityCachedSuccess()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Tracking');

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
            ->with('tracking_1')
            ->willReturn(true);
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->with('tracking_1')
            ->willReturn($cacheItem);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $actual = $service->getEntity($id);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TrackingService
     */
    public function testGetEntityNotCachedSuccess()
    {
        $id = 1;

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->with('tracking_1')
            ->willReturn(false);

        $entity = $this
            ->createMock('App\Entity\Tracking');

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $actual = $service->getEntity($id);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TrackingService
     */
    public function testGetEntityFailure()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Tracking');

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->with('tracking_1')
            ->willReturn(false);

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $actual = $service->getEntity($id);
        $expected = null;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TrackingService
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
            ->createMock('App\Entity\Tracking');

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [], [], 1)
            ->willReturn([$entity]);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $actual = $service->getEntityList($params, $offset);
        $expected = [$entity];

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TrackingService
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
            ->createMock('App\Entity\Tracking');

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [], [], 1)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $actual = $service->getEntityList($params, $offset);
        $expected = null;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TrackingService
     */
    public function testEntityUpdateSuccess()
    {
        $id = 1;
        $tracking = '{
            "trucker_id": 1,
            "from": {
                "street_name": "Rua",
                "street_number": "100",
                "neighborhood": "Bairro",
                "zip_code": "01234567",
                "city": "Sao Paulo",
                "state": "SP"
            },
            "to": {
                "street_name": "Avenida",
                "street_number": "1000",
                "neighborhood": "Vila",
                "zip_code": "76543210",
                "city": "Manaus",
                "state": "AM"
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02"
        }';

        $truckerEntity = $this
            ->createMock('App\Entity\Trucker');
        $truckerEntity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $addressEntity = $this
            ->createMock('App\Entity\Address');

        $entity = $this
            ->createMock('App\Entity\Tracking');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $entity
            ->expects($this->exactly(2))
            ->method('getTrucker')
            ->willReturn($truckerEntity);
        $entity
            ->expects($this->once())
            ->method('setTrucker')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('getFromAddress')
            ->willReturn($addressEntity);
        $entity
            ->expects($this->once())
            ->method('getToAddress')
            ->willReturn($addressEntity);
        $entity
            ->expects($this->once())
            ->method('setFromAddress')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('setToAddress')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('getCheckIn')
            ->willReturn("2020-01-01 01:00:00");
        $entity
            ->expects($this->once())
            ->method('setCheckIn')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('getCheckOut')
            ->willReturn("2020-01-01 02:00:00");
        $entity
            ->expects($this->once())
            ->method('setCheckOut')
            ->willReturn($entity);

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($tracking)
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
            ->with('tracking_1')
            ->willReturn($cacheItem);
        $cache
            ->expects($this->once())
            ->method('save');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $actual = $service->entityUpdate($tracking, $id);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TrackingService
     * @expectedException \Exception
     */
    public function testEntityUpdateUnavailableEntity()
    {
        $id = 1;
        $tracking = '{
            "trucker_id": 1,
            "from": {
                "street_name": "Rua",
                "street_number": "100",
                "neighborhood": "Bairro",
                "zip_code": "01234567",
                "city": "Sao Paulo",
                "state": "SP"
            },
            "to": {
                "street_name": "Avenida",
                "street_number": "1000",
                "neighborhood": "Vila",
                "zip_code": "76543210",
                "city": "Manaus",
                "state": "AM"
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02"
        }';

        $entity = $this
            ->createMock('App\Entity\Tracking');

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $service->entityUpdate($tracking, 1);
    }

    /**
     * @covers TrackingService
     */
    public function testDeleteEntitySuccess()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Tracking');

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
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
            ->with('tracking_1');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $service->deleteEntity($id);
    }

    /**
     * @covers TrackingService
     * @expectedException \Exception
     */
    public function testAlreadyDeletedEntity()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\Tracking');

        $repository = $this
            ->createMock('App\Repository\TrackingRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TrackingFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $truckerRepository = $this
            ->createMock('App\Repository\TruckerRepository');

        $service = new TrackingService(
            $entityManager,
            $repository,
            $factory,
            $cache,
            $truckerRepository
        );

        $service->deleteEntity($id);
    }
}