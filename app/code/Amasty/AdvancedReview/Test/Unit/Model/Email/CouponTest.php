<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Model\Email;

use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\Email\Coupon;
use Amasty\AdvancedReview\Test\Unit\Traits;
use Magento\Framework\DataObject;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class CouponTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetwgroupCollectionnObjects)
 * phpcs:ignoreFile
 */
class CouponTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var MockObject|Config
     */
    private $configHelper;

    /**
     * @var MockObject|CollectionFactory
     */
    private $groupCollection;

    /**
     * @var MockObject|Coupon
     */
    private $coupon;

    protected function setUp()
    {
        $this->configHelper = $this->createMock(Config::class);
        $this->groupCollection = $this->createMock(CollectionFactory::class);
        $this->coupon = $this->createPartialMock(Coupon::class, ['generateCoupon', 'getDaysMessage']);

        $this->setProperty($this->coupon, 'configHelper' , $this->configHelper);
        $this->setProperty($this->coupon, 'groupCollectionFactory' , $this->groupCollection);
    }

    /**
     * @covers \Amasty\AdvancedReview\Model\Email\Coupon::getCouponMessage
     *
     * @dataProvider getCouponMessageDataProvider
     */
    public function testGetCouponMessage(
        $isAllowCoupons,
        $isNeedReview,
        $resultTest,
        $couponCode = '',
        $day = '',
        $dayMessage = ''
    ) {
        $this->configHelper->expects($this->any())->method('isAllowCoupons')->willReturn($isAllowCoupons);
        $this->configHelper->expects($this->any())->method('isNeedReview')->willReturn($isNeedReview);
        $this->configHelper->expects($this->any())->method('getModuleConfig')->willReturn($day);
        $this->coupon->expects($this->any())->method('generateCoupon')->willReturn($couponCode);
        $this->coupon->expects($this->any())->method('getDaysMessage')->willReturn($dayMessage);

        $result = $this->invokeMethod($this->coupon, 'getCouponMessage', ['']);
        if (is_object($result)) {
            $result = $result->render();
        }
        $this->assertEquals($resultTest, $result);
    }

    /**
     * @covers \Amasty\AdvancedReview\Model\Email\Coupon::getCustomerGroupIds
     *
     * @dataProvider getCustomerGroupIdsDataProvider
     */
    public function testGetCustomerGroupIds($customerGroups, $groupCollection, $customerGroupIds)
    {
        $this->configHelper->expects($this->any())->method('getCustomerGroups')->willReturn($customerGroups);
        $groupObjects = [];
        foreach ($groupCollection as $group) {
            $groupObjects[] = $this->getObjectManager()->getObject(
                DataObject::class,
                ['data' => $group]
            );
        }
        $this->groupCollection->expects($this->any())->method('create')->willReturn($groupObjects);

        $result = $this->invokeMethod($this->coupon, 'getCustomerGroupIds', ['']);
        $this->assertEquals($customerGroupIds, $result);
    }

    /**
     * Data provider for getCouponMessage test
     * @return array
     */
    public function getCouponMessageDataProvider()
    {
        return [
            [
                true,
                true,
                'It will take only a few minutes, just click the \'Leave a review\' button below. And please kindly '
                . 'keep in mind, that you will receive a discount coupon after your review is approved.'
            ],
            [
                false,
                true,
                'It will take only a few minutes, just click the \'Leave a review\' button below.'
            ],
            [
                true,
                false,
                'It will take only a few minutes, just click the \'Leave a review\' button below.<br>To make the '
                . 'process more pleasant we are happy to grant you a discount coupon code, which can already be used '
                . 'for your next purchase. Here it is: CouponCode (please kindly keep in mind that it will expire in '
                . '5 days).',
                'CouponCode',
                '5',
                __('please kindly keep in mind that it will expire in 5 days')
            ]
        ];
    }

    /**
     * Data provider for getCustomerGroupIds test
     * @return array
     */
    public function getCustomerGroupIdsDataProvider()
    {
        return [
            [false, [['name' => 1, 'id' => 3]], [3]],
            [null, [['name' => 5, 'id' => 5]], [5]],
            [[1, 3], [['email' => 7, 'id' => 7]], [1, 3]]
        ];
    }
}
