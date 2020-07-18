<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Block\Widget;

use Amasty\AdvancedReview\Block\Widget\Reviews;
use Amasty\AdvancedReview\Model\Indexer\Catalog\Category\Product\TableResolver;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Test\Unit\Traits;
use Magento\Framework\Registry;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ReviewsTest
 *
 * @see Reviews
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ReviewsTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Reviews::getReviewsCollection
     */
    public function testGetReviewsCollection()
    {
        list($block, $reviewCollection) = $this->prepareDataForCreateCollection();

        $this->assertNotNull($block->getReviewsCollection());

        $this->setProperty($block, 'reviewsCollection', 'test', Reviews::class);
        $this->assertEquals('test', $block->getReviewsCollection());
    }

    /**
     * @covers Reviews::setTemplate
     */
    public function testSetTemplate()
    {
        $block = $this->getObjectManager()->getObject(Reviews::class);
        $block->setTemplate('');
        $this->assertEquals(Reviews::COMMON_TEMPLATE, $block->getTemplate());
        $block->setTemplate(Reviews::LAYOUT_CONTENT_TEMPLATE);
        $this->assertEquals('grid', $block->getContainerPosition());
        $block->setTemplate(Reviews::LAYOUT_SIDEBAR_TEMPLATE);
        $this->assertEquals('sidebar', $block->getContainerPosition());
    }

    /**
     * @covers Reviews::createCollection
     */
    public function testCreateCollection()
    {
        list($block, $reviewCollection) = $this->prepareDataForCreateCollection();
        $block->expects($this->any())->method('getHigherThan')->will($this->onConsecutiveCalls(1, 0));
        $block->expects($this->any())->method('getReviewType')->will($this->onConsecutiveCalls(0, 1));
        $reviewCollection->expects($this->once())->method('setFlag')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('setOrder')->willReturn($reviewCollection);
        $reviewCollection->expects($this->once())->method('setDateOrder')->willReturn($reviewCollection);

        $this->invokeMethod($block, 'createCollection');
        $this->invokeMethod($block, 'createCollection');
    }

    private function prepareDataForCreateCollection()
    {
        $block = $this->createPartialMock(Reviews::class, ['getLimit', 'getHigherThan', 'getReviewType']);
        $reviewCollection = $this->createPartialMock(
            ReviewCollection::class,
            ['create', 'addStoreFilter', 'addStatusFilter', 'setPageSize', 'getSelect', 'having', 'setFlag', 'setOrder', 'setDateOrder']
        );
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeMock = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);
        $request = $this->getMockBuilder(
            \Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getFullActionName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $block->expects($this->any())->method('getLimit')->willReturn(1);
        $reviewCollection->expects($this->any())->method('create')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addStoreFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('addStatusFilter')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('setPageSize')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('getSelect')->willReturn($reviewCollection);
        $reviewCollection->expects($this->any())->method('having')->willReturn($reviewCollection);
        $storeMock->expects($this->any())->method('getId')->willReturn(1);
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($storeMock);

        $this->setProperty($block, 'reviewCollectionFactory', $reviewCollection, Reviews::class);
        $this->setProperty($block, '_storeManager', $storeManagerMock, Reviews::class);
        $this->setProperty($block, '_request', $request, Reviews::class);

        return [$block, $reviewCollection];
    }

    /**
     * @covers Reviews::getEntityFilter
     */
    public function testGetEntityFilter()
    {
        $block = $this->createPartialMock(Reviews::class, ['isCurrentCategoryOnly']);
        $request = $this->getMockBuilder(
            \Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getFullActionName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $registry = $this->createMock(Registry::class);
        $category = $this->createMock(\Magento\Catalog\Model\Category::class);
        $tableResolver = $this->createMock(TableResolver::class);

        $request->expects($this->any())->method('getFullActionName')->will(
            $this->onConsecutiveCalls('test', 'catalog_category_view', 'catalog_category_view')
        );
        $registry->expects($this->any())->method('registry')->willReturn($category);
        $category->expects($this->any())->method('getId')->willReturn(1);
        $tableResolver->expects($this->any())->method('getProductIds')->willReturn(true);
        $block->expects($this->any())->method('isCurrentCategoryOnly')->will($this->onConsecutiveCalls(0, 1));

        $this->setProperty($block, '_request', $request, Reviews::class);
        $this->setProperty($block, 'registry', $registry, Reviews::class);
        $this->setProperty($block, 'tableResolver', $tableResolver, Reviews::class);

        $this->assertNull($this->invokeMethod($block, 'getEntityFilter'));
        $this->assertNull($this->invokeMethod($block, 'getEntityFilter'));
        $this->assertTrue($this->invokeMethod($block, 'getEntityFilter'));
    }

    /**
     * @covers Reviews::getLimit
     */
    public function testGetLimit()
    {
        $block = $this->getObjectManager()->getObject(Reviews::class);
        $this->assertEquals(Reviews::DEFAULT_LIMIT, $block->getLimit());
        $block->setData(Reviews::LIMIT, 1.5);
        $this->assertEquals(1, $block->getLimit());
    }

    /**
     * @covers Reviews::getReviewMessage
     */
    public function testGetReviewMessage()
    {
        $data = 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest' .
            'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttes';
        $result = 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttes...';
        $block = $this->getObjectManager()->getObject(Reviews::class);
        $this->assertEquals('test', $block->getReviewMessage('test'));
        $this->assertEquals($result, $block->getReviewMessage($data));
    }
}
