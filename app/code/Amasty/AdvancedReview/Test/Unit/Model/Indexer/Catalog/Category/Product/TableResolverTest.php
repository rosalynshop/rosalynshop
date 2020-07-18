<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Model\Indexer\Catalog\Category\Product;

use Amasty\AdvancedReview\Model\Indexer\Catalog\Category\Product\TableResolver;
use Amasty\AdvancedReview\Test\Unit\Traits;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\DimensionFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class TableResolverTest
 *
 * @see TableResolver
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class TableResolverTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers TableResolver::getTableName
     */
    public function testGetTableName()
    {
        $dimensionFactory = $this->createMock(DimensionFactory::class);
        $tableResolver = $this->createMock(IndexScopeResolver::class);
        $resource = $this->getMockBuilder(ResourceConnection::class)
            ->setMethods(['getConnection', 'isTableExists', 'getTableName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $model = $this->getObjectManager()->getObject(
            TableResolver::class,
            [
                'dimensionFactory' => $dimensionFactory,
                'resource' => $resource,
                'tableResolver' => $tableResolver,
            ]
        );

        $dimensionFactory->expects($this->any())->method('create')->willReturn(1);
        $tableResolver->expects($this->any())->method('resolve')->willReturn(true);
        $resource->expects($this->any())->method('getConnection')->willReturn($resource);
        $resource->expects($this->any())->method('isTableExists')->willReturnOnConsecutiveCalls(false, true);
        $resource->expects($this->any())->method('getTableName')->willReturnArgument(0);

        $this->assertEquals(TableResolver::MAIN_INDEX_TABLE, $model->getTableName(1));
        $this->assertTrue($model->getTableName(1));
    }
}
