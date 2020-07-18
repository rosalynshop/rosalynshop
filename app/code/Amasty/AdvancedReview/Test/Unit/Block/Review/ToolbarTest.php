<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Block\Review;

use Amasty\AdvancedReview\Block\Review\Toolbar;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ToolbarTest
 *
 * @see Toolbar
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ToolbarTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Toolbar::isSortingEnabled
     *
     * @dataProvider isSortingEnabledDataProvider
     */
    public function testIsSortingEnabled($data, $result)
    {
        $block = $this->createPartialMock(Toolbar::class, []);
        $config = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $config->expects($this->any())->method('getSortingOptions')->willReturn($data);
        $this->setProperty($block, 'config', $config, Toolbar::class);
        $this->assertEquals($result, $block->isSortingEnabled());
    }

    /**
     * @covers Toolbar::isFilteringEnabled
     *
     * @dataProvider isSortingEnabledDataProvider
     */
    public function testIsFilteringEnabled($data, $result)
    {
        $block = $this->createPartialMock(Toolbar::class, []);
        $config = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $config->expects($this->any())->method('getFilteringOptions')->willReturn($data);
        $this->setProperty($block, 'config', $config, Toolbar::class);
        $this->assertEquals($result, $block->isFilteringEnabled());
    }

    /**
     * @covers Toolbar::getAvailableOrders
     *
     * @dataProvider getAvailableOrdersDataProvider
     *
     * @param $sorting
     * @param $all
     * @param $result
     *
     * @throws \ReflectionException
     */
    public function testGetAvailableOrders($sorting, $all, $result)
    {
        $block = $this->createPartialMock(Toolbar::class, []);

        $config = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $config->expects($this->any())->method('getSortingOptions')->willReturn($sorting);
        $this->setProperty($block, 'config', $config, Toolbar::class);

        $sort = $this->createMock(\Amasty\AdvancedReview\Model\Sources\Sort::class);
        $sort->expects($this->any())->method('toOptionArray')->willReturn($all);
        $this->setProperty($block, 'sortModel', $sort, Toolbar::class);

        $this->assertEquals($result, $block->getAvailableOrders());
    }

    /**
     * @covers Toolbar::getAvailableFilters
     *
     * @dataProvider getAvailableOrdersDataProvider
     *
     * @param $sorting
     * @param $all
     * @param $result
     *
     * @throws \ReflectionException
     */
    public function testGetAvailableFilters($sorting, $all, $result)
    {
        $block = $this->createPartialMock(Toolbar::class, []);

        $config = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $config->expects($this->any())->method('getFilteringOptions')->willReturn($sorting);
        $this->setProperty($block, 'config', $config, Toolbar::class);

        $filterModel = $this->createMock(\Amasty\AdvancedReview\Model\Sources\Filter::class);
        $filterModel->expects($this->any())->method('toOptionArray')->willReturn($all);
        $this->setProperty($block, 'filterModel', $filterModel, Toolbar::class);

        $this->assertEquals($result, $block->getAvailableFilters());
    }

    /**
     * Data provider for testGetAvailableOrders test
     * @return array
     */
    public function getAvailableOrdersDataProvider()
    {
        return [
            [['test'], [['value' => 'test', 'label' => 'Test'], ['value' => 'test1', 'label' => 'Test1']], ['test' => 'Test']],
            [['test1'], [['value' => 'test', 'label' => 'Test'], ['value' => 'test1', 'label' => 'Test1']], ['test1' => 'Test1']],
            [[], [['value' => 'test', 'label' => 'Test'], ['value' => 'test1', 'label' => 'Test1']], []]
        ];
    }

    /**
     * Data provider for testIsSortingEnabled test
     * @return array
     */
    public function isSortingEnabledDataProvider()
    {
        return [
            [['test'], true],
            [[], false],
            [['test1', 'test2'], true]
        ];
    }

    /**
     * @covers Toolbar::getCurrentDirection
     */
    public function testGetCurrentDirection()
    {
        $block = $this->createPartialMock(Toolbar::class, ['getRequest']);
        $request = $this->createPartialMock(\Magento\Framework\App\Request\Http::class, ['getParam']);
        $request->expects($this->once())->method('getParam')->willReturn('asc');
        $block->expects($this->once())->method('getRequest')->willReturn($request);

        $this->assertEquals('ASC', $block->getCurrentDirection());

        $block = $this->createPartialMock(Toolbar::class, ['getRequest']);
        $request = $this->createPartialMock(\Magento\Framework\App\Request\Http::class, ['getParam']);
        $request->expects($this->once())->method('getParam')->willReturn('desc');
        $block->expects($this->once())->method('getRequest')->willReturn($request);

        $this->assertEquals('DESC', $block->getCurrentDirection());
    }

    /**
     * @covers Toolbar::isToolbarDisplayed
     */
    public function testIsToolbarDisplayed()
    {
        $block = $this->createPartialMock(Toolbar::class, ['isSortingEnabled', 'isFilteringEnabled']);
        $block->expects($this->once())->method('isSortingEnabled')->willReturn(1);
        $block->expects($this->never())->method('isFilteringEnabled')->willReturn(1);
        $this->assertTrue($block->isToolbarDisplayed());

        $block = $this->createPartialMock(Toolbar::class, ['isSortingEnabled', 'isFilteringEnabled']);
        $block->expects($this->once())->method('isSortingEnabled')->willReturn(0);
        $block->expects($this->once())->method('isFilteringEnabled')->willReturn(1);
        $this->assertTrue($block->isToolbarDisplayed());

        $block = $this->createPartialMock(Toolbar::class, ['isSortingEnabled', 'isFilteringEnabled']);
        $block->expects($this->once())->method('isSortingEnabled')->willReturn(0);
        $block->expects($this->once())->method('isFilteringEnabled')->willReturn(0);
        $this->assertFalse($block->isToolbarDisplayed());
    }
}
