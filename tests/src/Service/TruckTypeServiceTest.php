<?php

namespace App\Tests\Service;

use App\Service\TruckTypeService;
use PHPUnit\Framework\TestCase;

class TruckTypeServiceTest extends TestCase
{
    /**
     * @covers TruckTypeService
     */
    public function testCreateEntitySuccess()
    {
        $truckType = '{"name": "Caminhão"}';

        $entity = $this
            ->createMock('App\Entity\TruckType');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($truckType)
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
            ->with('trucktype_1')
            ->willReturn($cacheItem);
        $cache
            ->expects($this->once())
            ->method('save');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $actual = $service->createEntity($truckType);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckTypeService
     * @expectedException \Exception
     */
    public function testCreateEntityFailure()
    {
        $truckType = '{"invalid": "data"}';

        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($truckType)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $service->createEntity($truckType);
    }

    /**
     * @covers TruckTypeService
     */
    public function testGetEntityCachedSuccess()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\TruckType');

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
            ->with('trucktype_1')
            ->willReturn(true);
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->with('trucktype_1')
            ->willReturn($cacheItem);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $actual = $service->getEntity($id);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckTypeService
     */
    public function testGetEntityNotCachedSuccess()
    {
        $id = 1;

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->with('trucktype_1')
            ->willReturn(false);

        $entity = $this
            ->createMock('App\Entity\TruckType');

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $actual = $service->getEntity($id);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckTypeService
     */
    public function testGetEntityFailure()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\TruckType');

        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');
        $cache
            ->expects($this->once())
            ->method('hasItem')
            ->with('trucktype_1')
            ->willReturn(false);

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $actual = $service->getEntity($id);
        $expected = null;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckTypeService
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
            ->createMock('App\Entity\TruckType');

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [], [], 1)
            ->willReturn([$entity]);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $actual = $service->getEntityList($params, $offset);
        $expected = [$entity];

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckTypeService
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
            ->createMock('App\Entity\TruckType');

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [], [], 1)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $actual = $service->getEntityList($params, $offset);
        $expected = null;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckTypeService
     */
    public function testEntityUpdateSuccess()
    {
        $id = 1;
        $newTruckType = '{"name": "Carreta"}';

        $entity = $this
            ->createMock('App\Entity\TruckType');
        $entity
            ->expects($this->once())
            ->method('getName')
            ->willReturn("Carreta");
        $entity
            ->expects($this->once())
            ->method('setName')
            ->willReturn($entity);
        $entity
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($entity);

        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');
        $factory
            ->expects($this->once())
            ->method('create')
            ->with($newTruckType)
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
            ->with('trucktype_1')
            ->willReturn($cacheItem);
        $cache
            ->expects($this->once())
            ->method('save');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $trackingRepository = $this
            ->createMock('App\Repository\TrackingRepository');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $actual = $service->entityUpdate($newTruckType, 1);
        $expected = $entity;

        $this->assertEquals($actual, $expected);
    }

    /**
     * @covers TruckTypeService
     * @expectedException \Exception
     */
    public function testEntityUpdateUnavailableEntity()
    {
        $id = 1;

        $newTruckType = '{"name": "Carreta"}';

        $entity = $this
            ->createMock('App\Entity\TruckType');

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $service->entityUpdate($newTruckType, 1);
    }

    /**
     * @covers TruckTypeService
     */
    public function testDeleteEntitySuccess()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\TruckType');

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
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
            ->with('trucktype_1');

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $service->deleteEntity($id);
    }

    /**
     * @covers TruckTypeService
     * @expectedException \Exception
     */
    public function testAlreadyDeletedEntity()
    {
        $id = 1;

        $entity = $this
            ->createMock('App\Entity\TruckType');

        $repository = $this
            ->createMock('App\Repository\TruckTypeRepository');
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn(null);

        $entityManager = $this
            ->createMock('Doctrine\ORM\EntityManagerInterface');
        $factory = $this
            ->createMock('App\Helper\TruckTypeFactory');
        $cache = $this
            ->createMock('Psr\Cache\CacheItemPoolInterface');

        $service = new TruckTypeService(
            $entityManager,
            $repository,
            $factory,
            $cache
        );

        $service->deleteEntity($id);
    }
}