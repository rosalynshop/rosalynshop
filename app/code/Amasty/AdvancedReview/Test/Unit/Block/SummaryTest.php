<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Block;

use Amasty\AdvancedReview\Block\Summary;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SummaryTest
 *
 * @see Summary
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class SummaryTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Summary::getReviewsCollection
     */
    public function testGetReviewsCollection()
    {
        $block = $this->createPartialMock(Summary::class, []);
        $reviewCollection = $this->createPartialMock(
            ReviewCollection::class,
            ['create', 'addStoreFilter', 'addStatusFilter', 'addEntityFilter', 'setDateOrder']
        );
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeMock = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);

        $storeMock->expects($this->any())->method('getId')->willReturn(1);
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($storeMock);
        $reviewCollection->expects($this->once())->method('create')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('addStoreFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('addStatusFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('addEntityFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('setDateOrder')->willReturn($reviewCollection);

        $this->setProperty($block, '_storeManager', $storeManagerMock, Summary::class);
        $this->setProperty($block, 'reviewsColFactory', $reviewCollection, Summary::class);
        $this->setProperty($block, 'product', $storeMock, Summary::class);

        $block->getReviewsCollection();

        $this->setProperty($block, 'reviewsCollection', 'test', Summary::class);
        $this->assertEquals('test', $block->getReviewsCollection());
    }

    /**
     * @covers Summary::getRatingSummary
     */
    public function testGetRatingSummary()
    {
        $block = $this->createPartialMock(Summary::class, []);
        $product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->setMethods(['getRatingSummary'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $product->expects($this->any())->method('getRatingSummary')->willReturnOnConsecutiveCalls(0, $product, 4);

        $this->setProperty($block, 'product', $product, Summary::class);

        $this->assertNull($block->getRatingSummary());
        $this->assertEquals(4, $block->getRatingSummary());
    }

    /**
     * @covers Summary::getDetailedSummary
     */
    public function testGetDetailedSummary()
    {
        $result1 = [
            '5' => 0,
            '4' => 0,
            '3' => 0,
            '2' => 0,
            '1' => 0
        ];
        $result2 = [
            '5' => 0,
            '4' => 0,
            '3' => 0,
            '2' => 0,
            '1' => 1
        ];
        $block = $this->createPartialMock(Summary::class, ['getReviewsCollection', 'getDisplayedCollection']);
        $reviewCollection = $this->createPartialMock(
            ReviewCollection::class,
            ['getFlag', 'setFlag']
        );
        $object = $this->getObjectManager()->getObject(\Magento\Review\Model\Review::class);
        $ratingFactory = $this->getMockBuilder(\Magento\Review\Model\RatingFactory::class)
            ->setMethods(['getReviewSummary', 'create'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $object->setData('count', 5);
        $object->setData('sum', 100);
        $block->expects($this->any())->method('getReviewsCollection')->willReturnOnConsecutiveCalls([], [$object]);
        $block->expects($this->any())->method('getDisplayedCollection')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('getFlag')->willReturnOnConsecutiveCalls(false, true);
        $reviewCollection->expects($this->once())->method('setFlag');
        $ratingFactory->expects($this->once())->method('create')->willReturn($ratingFactory);
        $ratingFactory->expects($this->once())->method('getReviewSummary')->willReturn($object);

        $this->setProperty($block, 'ratingFactory', $ratingFactory, Summary::class);

        $this->assertEquals($result1, $block->getDetailedSummary());
        $this->assertEquals($result2, $block->getDetailedSummary());
    }

    /**
     * @covers Summary::getRatingSummaryValue
     */
    public function testGetRatingSummaryValue()
    {
        $block = $this->createPartialMock(Summary::class, ['getRatingSummary']);
        $block->expects($this->any())->method('getRatingSummary')->willReturn(10);

        $this->assertEquals(0.5, $block->getRatingSummaryValue());
    }

    /**
     * @covers Summary::getRecomendedPercent
     */
    public function testGetRecomendedPercent()
    {
        $block = $this->createPartialMock(Summary::class, []);
        $reviewCollection = $this->createPartialMock(
            ReviewCollection::class,
            ['create', 'addStoreFilter', 'addStatusFilter', 'addEntityFilter', 'setDateOrder', 'addFieldToFilter', 'getSize']
        );
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeMock = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);

        $storeMock->expects($this->any())->method('getId')->willReturn(1);
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($storeMock);
        $reviewCollection->expects($this->any())->method('create')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addStoreFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addStatusFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addEntityFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addFieldToFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('getSize')->willReturn(10);

        $this->setProperty($block, '_storeManager', $storeManagerMock, Summary::class);
        $this->setProperty($block, 'reviewsColFactory', $reviewCollection, Summary::class);
        $this->setProperty($block, 'product', $storeMock, Summary::class);

        $this->assertEquals(100, $block->getRecomendedPercent());
    }
}
