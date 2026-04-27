<?php

/**
 * @since       15.09.2024 - 08:20
 *
 * @author      Patrick Froch <info@easySolutionsIT.de>
 *
 * @see         http://easySolutionsIT.de
 *
 * @copyright   e@sy Solutions IT 2024
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Library\Collections;

use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Iterator\CollectionIterator;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ArrayCollectionTest extends TestCase
{


    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serializeHelper;


    /**
     * @var (ConverterHelper&MockObject)|MockObject
     */
    private $converterHelper;


    /**
     * @var ArrayCollection
     */
    private ArrayCollection $collection;


    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->serializeHelper      = $this->getMockBuilder(SerializeHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->converterHelper      = $this->getMockBuilder(ConverterHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collection = new ArrayCollection(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->converterHelper
        );
    }


    public function testGetValue(): void
    {
        $key        = 'test';
        $data       = [12, 34, 'TestValue'];

        $collection = $this->getMockBuilder(ArrayCollection::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->converterHelper->expects($this->once())
                              ->method('convertArrayToCollection')
                              ->with(\serialize($data))
                              ->willReturn($collection);

        $this->serializeHelper->expects($this->once())
                              ->method('serialize')
                              ->with($data)
                              ->willReturn(\serialize($data));

        $this->collection->setValue($key, $data);

        $this->assertSame($collection, $this->collection->getValue($key));
    }


    public function testgetIterator(): void
    {
        $iterator = $this->getMockBuilder(CollectionIterator::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $this->collectionFactory->expects($this->once())
                                ->method('createCollectionIterator')
                                ->with($this->collection)
                                ->willReturn($iterator);

        $this->assertSame($iterator, $this->collection->getIterator());
    }


    public function testCurrent(): void
    {
        $key        = 'test';
        $data       = [12, 34, 'TestValue'];

        $collection = $this->getMockBuilder(ArrayCollection::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->converterHelper->expects($this->once())
                              ->method('convertArrayToCollection')
                              ->willReturn($collection);

        $this->collection->setValue($key, $data);

        $this->assertSame($collection, $this->collection->current());
    }
}
