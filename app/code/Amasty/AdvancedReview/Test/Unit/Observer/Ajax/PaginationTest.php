<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Observer\Ajax;

use Amasty\AdvancedReview\Observer\Ajax\Pagination;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class PaginationTest
 *
 * @see Pagination
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class PaginationTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var \Magento\Review\Controller\Product|MockObject
     */
    private $controllerAction;

    /**
     * @var \Magento\Catalog\Model\Product|MockObject
     */
    private $product;

    /**
     * @covers Pagination::execute
     *
     * @dataProvider executeDataProvider
     *
     * @throws \ReflectionException
     */
    public function testExecute($actionName, $isAjax, $count)
    {
        /** @var \Magento\Framework\Event\Observer|MockObject $observer */
        $observer = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProduct', 'getControllerAction'])
            ->getMock();

        $this->product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $this->product->expects($this->any())->method('getProductUrl')->willReturn('test/url');

        $observer->expects($this->any())->method('getProduct')->willReturnCallback(
            function () {
                return $this->product;
            }
        );
        $this->controllerAction = $this->getMockBuilder(\Magento\Review\Controller\Product::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $observer->expects($this->any())->method('getControllerAction')->willReturnCallback(
            function () {
                return $this->controllerAction;
            }
        );

        /** @var \Magento\Framework\App\RequestInterface|MockObject $request */
        $request = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getActionName', 'isAjax'])
            ->getMockForAbstractClass();
        $request->expects($this->any())->method('getActionName')->willReturn($actionName);
        $request->expects($this->any())->method('isAjax')->willReturn($isAjax);

        /** @var \Magento\Framework\App\ResponseInterface|MockObject $response */
        $response = $this->getMockBuilder(\Magento\Framework\App\ResponseInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setRedirect'])
            ->getMockForAbstractClass();
        $response->expects($this->any())->method('setRedirect');
        $response->expects($this->exactly($count))->method('setRedirect');

        $this->setProperty($this->controllerAction, '_request', $request, \Magento\Framework\App\Action\AbstractAction::class);
        $this->setProperty($this->controllerAction, '_response', $response, \Magento\Framework\App\Action\AbstractAction::class);

        /** @var Pagination $pagination */
        $pagination = $this->getObjectManager()->getObject(Pagination::class);
        $pagination->execute($observer);
    }

    /**
     * Data provider for execute test
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            ['listAjax', false, 1],
            ['listAjax', true, 0],
            ['listAjax1', false, 0]
        ];
    }
}
