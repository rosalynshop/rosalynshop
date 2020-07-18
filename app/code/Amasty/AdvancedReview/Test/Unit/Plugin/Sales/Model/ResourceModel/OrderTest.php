<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Plugin\Sales\Model\ResourceModel;

use Amasty\AdvancedReview\Plugin\Sales\Model\ResourceModel\Order;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Sales\Model\ResourceModel\Order as OrderSubject;

/**
 * Class OrderTest
 *
 * @see Order
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class OrderTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    const STATUS_PENDING = 'Pending';

    /**
     * @covers Order::aroundSave
     *
     * @throws \ReflectionException
     */
    public function testAroundSave()
    {
        /** @var \Amasty\AdvancedReview\Helper\Config|MockObject $config */
        $config = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $config->expects($this->any())->method('getTriggerOrderStatus')->willReturn(self::STATUS_PENDING);
        $config->expects($this->any())->method('isReminderEnabled')->willReturn(true);

        $plugin = $this->getObjectManager()->getObject(Order::class, ['config' => $config]);
        $subject = $this->createMock(OrderSubject::class);

        $callbackMock = $this->getMockBuilder(\stdClass::class)
            ->getMock();
        /** @var \Magento\Framework\Model\AbstractModel|MockObject $object */
        $object = $this->getMockBuilder(\Magento\Framework\Model\AbstractModel::class)
            ->setMethods(['getStatus', 'getOrigData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $object->expects($this->any())->method('getStatus')->willReturn(self::STATUS_PENDING);
        $object->expects($this->any())->method('getOrigData')->with('status')->will($this->returnValue(self::STATUS_PENDING));

        $closure = function () use ($object) {
            return $object;
        };

        $this->assertSame($object, $plugin->aroundSave($subject, $closure, $object));
    }
}
