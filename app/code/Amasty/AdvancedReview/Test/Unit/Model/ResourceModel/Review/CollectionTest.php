<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Model\ResourceModel\Review;

use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection;
use Amasty\AdvancedReview\Test\Unit\Traits;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class CollectionTest
 *
 * @see Collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class CollectionTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var MockObject|Collection
     */
    private $model;

    protected function setUp()
    {
        $this->model = $this->createPartialMock(Collection::class, ['getTable', 'addFilter']);
        $this->model->expects($this->any())->method('getTable')->willReturn(1);
        $select = $this->createPartialMock(\Magento\Framework\DB\Select::class, ['join']);
        $select->expects($this->any())->method('join');
        $this->setProperty($this->model, '_select', $select);
    }

    /**
     * @covers Collection::_renderFiltersBefore
     */
    public function testRenderFiltersBefore()
    {
        $this->setProperty($this->model, '_flags', ['test1' => 1, 'test2' => 2]);
        $this->invokeMethod($this->model, '_renderFiltersBefore');
        $parts = $this->getProperty($this->model->getSelect(), '_parts');
        $this->assertArrayNotHasKey('group', $parts);

        $this->setProperty($this->model, '_flags', ['filter_by_stars' => 1, 'test2' => 2]);
        $this->invokeMethod($this->model, '_renderFiltersBefore');
        $parts = $this->getProperty($this->model->getSelect(), '_parts');
        $this->assertArrayHasKey('group', $parts);
    }

    /**
     * @covers Collection::getSize
     */
    public function testGetSize()
    {
        $this->setProperty($this->model, '_flags', ['test1' => 1, 'test2' => 2]);
        $this->setProperty($this->model, '_totalRecords', 5);
        $this->assertEquals(5, $this->model->getSize());
        $this->setProperty($this->model, '_flags', ['items_count' => 1, 'test2' => 2]);
        $this->assertEquals(1, $this->model->getSize());
    }

    /**
     * @covers Collection::addEntityFilter
     */
    public function testAddEntityFilter()
    {
        $connection = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['quoteInto'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->setProperty($this->model, '_conn', $connection);
        $this->model->expects($this->exactly(4))->method('addFilter');
        $connection->expects($this->any())->method('quoteInto');
        $this->model->addEntityFilter(1, 2);
        $this->model->addEntityFilter('test', [2]);

    }
}
