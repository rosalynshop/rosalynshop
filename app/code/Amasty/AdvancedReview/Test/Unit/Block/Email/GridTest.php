<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Block\Email;

use Amasty\AdvancedReview\Test\Unit\Traits;
use Amasty\AdvancedReview\Block\Email\Grid;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\DataObject;

/**
 * Class GridTest
 * @see Grid
 * phpcs:ignoreFile
 */
class GridTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Grid::getProductName
     */
    public function testGetProductName()
    {
        $block = $this->createPartialMock(Grid::class, []);

        $reviewObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['sku' => 'test', 'name' => 'name12345']]
        );
        $this->assertEquals('name12345 (test)', $block->getProductName($reviewObject));

        $reviewObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['sku' => 'test']]
        );
        $this->assertEquals('N/A', $block->getProductName($reviewObject));

        $reviewObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['entity_pk_value' => 6,]]
        );
        $productObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['sku' => 'test', 'name' => 'name12345']]
        );
        $productRepository = $this->createMock(\Magento\Catalog\Model\ProductRepository::class);
        $productRepository->expects($this->once())->method('getById')->willReturn($productObject);
        $this->setProperty($block, 'productRepository', $productRepository, Grid::class);
        $this->assertEquals('name12345 (test)', $block->getProductName($reviewObject));
    }

    /**
     * @covers Grid::getCustomerEmail
     */
    public function testGetCustomerEmail()
    {
        $block = $this->createPartialMock(Grid::class, []);

        $reviewObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['sku' => 'test', 'name' => 'name12345']]
        );
        $this->assertEquals('', $block->getCustomerEmail($reviewObject));

        $reviewObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['guest_email' => 'test@test.com']]
        );
        $this->assertEquals('(test@test.com)', $block->getCustomerEmail($reviewObject));

        $reviewObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['customer_id' => 6]]
        );
        $customerObject = $this->getObjectManager()->getObject(
            DataObject::class,
            ['data' => ['email' => 'test1@test.com']]
        );
        $customerRepository = $this->createMock(\Magento\Customer\Model\ResourceModel\CustomerRepository::class);
        $customerRepository->expects($this->once())->method('getById')->willReturn($customerObject);
        $this->setProperty($block, 'customerRepository', $customerRepository, Grid::class);
        $this->assertEquals('(test1@test.com)', $block->getCustomerEmail($reviewObject));
    }
}
