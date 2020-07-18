<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Helper;

use Amasty\AdvancedReview\Model\Sources\Frequency;
use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ConfigTest
 *
 * @see Config
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var MockObject|Config
     */
    private $helper;

    protected function setUp()
    {
        $this->helper = $this->createPartialMock(Config::class, ['getReminderFrequency']);
    }

    /**
     * @covers Config::isReminderPerCustomer
     *
     * @dataProvider isReminderPerCustomerDataProvider
     */
    public function testIsReminderPerCustomer($reminderFrequency, $reminderPerCustomer)
    {
        $this->helper->expects($this->any())->method('getReminderFrequency')->willReturn($reminderFrequency);

        $result = $this->helper->isReminderPerCustomer();

        $this->assertEquals($reminderPerCustomer, $result);
    }

    /**
     * @covers Config::isReminderPerProduct
     *
     * @dataProvider isReminderPerProductDataProvider
     */
    public function testIsReminderPerProduct($reminderFrequency, $reminderPerProduct)
    {
        $this->helper->expects($this->any())->method('getReminderFrequency')->willReturn($reminderFrequency);

        $result = $this->helper->isReminderPerProduct();

        $this->assertEquals($reminderPerProduct, $result);
    }

    /**
     * Data provider for isReminderPerCustomer test
     * @return array
     */
    public function isReminderPerCustomerDataProvider()
    {
        return [
            [Frequency::PER_CUSTOMER, true],
            [Frequency::PER_PRODUCT, false],
            [Frequency::PER_ORDER, false],
            [65465, false]
        ];
    }

    /**
     * Data provider for isReminderPerProduct test
     * @return array
     */
    public function isReminderPerProductDataProvider()
    {
        return [
            [Frequency::PER_CUSTOMER, false],
            [Frequency::PER_PRODUCT, true],
            [Frequency::PER_ORDER, false],
            [65465, false]
        ];
    }
}
