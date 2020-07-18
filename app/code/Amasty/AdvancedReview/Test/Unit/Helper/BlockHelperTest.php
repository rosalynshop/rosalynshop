<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Helper;

use Amasty\AdvancedReview\Block\Widget\Reviews;
use Amasty\AdvancedReview\Helper\BlockHelper;
use Amasty\AdvancedReview\Test\Unit\Traits;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class BlockHelperTest
 *
 * @see BlockHelper
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class BlockHelperTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var MockObject|BlockHelper
     */
    private $helper;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $config;

    protected function setUp()
    {
        $this->config = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $blockFactory = $this->getMockBuilder(\Magento\Framework\View\Element\BlockFactory::class)
            ->setMethods(['createBlock', 'setReview', 'toHtml', 'setDisplayedCollection', 'setProduct', 'setReviewId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->helper = $this->createPartialMock(BlockHelper::class, ['getProductReviewUrl']);
        $stringUtils = $this->getObjectManager()->getObject(\Magento\Framework\Stdlib\StringUtils::class);

        $this->config->expects($this->any())->method('isAllowAnswer')->willReturn(true);
        $this->config->expects($this->any())->method('isRecommendFieldEnabled')->willReturn(true);
        $this->config->expects($this->any())->method('isAllowHelpful')->willReturnOnConsecutiveCalls(false, true);
        $this->config->expects($this->any())->method('isAllowImages')->willReturnOnConsecutiveCalls(false, true);
        $blockFactory->expects($this->any())->method('createBlock')->willReturn($blockFactory);
        $blockFactory->expects($this->any())->method('setReview')->willReturn($blockFactory);
        $blockFactory->expects($this->any())->method('toHtml')->willReturn('test');
        $blockFactory->expects($this->any())->method('setProduct')->willReturn($blockFactory);
        $blockFactory->expects($this->any())->method('setDisplayedCollection')->willReturn($blockFactory);
        $blockFactory->expects($this->any())->method('setReviewId')->willReturn($blockFactory);

        $this->setProperty($this->helper, 'config', $this->config, BlockHelper::class);
        $this->setProperty($this->helper, 'blockFactory', $blockFactory, BlockHelper::class);
        $this->setProperty($this->helper, 'stringUtils', $stringUtils, BlockHelper::class);
    }

    /**
     * @covers BlockHelper::getReviewAnswerHtml
     */
    public function testGetReviewAnswerHtml()
    {
        $review = $this->getObjectManager()->getObject(Reviews::class);
        $this->assertEquals('', $this->helper->getReviewAnswerHtml($review));
        $review->setAnswer('test');
        $this->assertEquals('test', $this->helper->getReviewAnswerHtml($review));
    }

    /**
     * @covers BlockHelper::getVerifiedBuyerHtml
     */
    public function testGetVerifiedBuyerHtml()
    {
        $review = $this->getObjectManager()->getObject(Reviews::class);
        $this->assertEquals('', $this->helper->getVerifiedBuyerHtml($review));
        $review->setVerifiedBuyer('test');
        $this->assertEquals('<div class="amreview-verified">Verified Buyer</div>', $this->helper->getVerifiedBuyerHtml($review));
    }

    /**
     * @covers BlockHelper::getRecommendedHtml
     */
    public function testGetRecommendedHtml()
    {
        $review = $this->getObjectManager()->getObject(Reviews::class);
        $this->assertEquals('', $this->helper->getRecommendedHtml($review));
        $review->setData('is_recommended', 1);
        $this->assertEquals(
            '<p class="amreview-recommended">I recommend this product</p>',
            $this->helper->getRecommendedHtml($review)
        );
    }

    /**
     * @covers BlockHelper::getHelpfulHtml
     */
    public function testGetHelpfulHtml()
    {
        $review = $this->getObjectManager()->getObject(Reviews::class);
        $this->assertEquals('', $this->helper->getHelpfulHtml($review));
        $review->setVerifiedBuyer('test');
        $this->assertEquals('test', $this->helper->getHelpfulHtml($review));
    }

    /**
     * @covers BlockHelper::getReviewsSummaryHtml
     */
    public function testGetReviewsSummaryHtml()
    {
        $product = $this->getObjectManager()->getObject(Product::class);
        $this->assertEquals('test', $this->helper->getReviewsSummaryHtml($product, true));
    }

    /**
     * @covers BlockHelper::getReviewImagesHtml
     */
    public function testGetReviewImagesHtml()
    {
        $this->assertEquals('', $this->helper->getHelpfulHtml(1));
        $this->assertEquals('test', $this->helper->getHelpfulHtml(1));
    }

    /**
     * @covers BlockHelper::cutLongDescription
     */
    public function testCutLongDescription()
    {
        $data = 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest' .
            'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest' .
            'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest' .
            'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest' .
            'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest' .
            'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest';
        $this->assertEquals('', $this->helper->cutLongDescription(''));
        $this->assertNotEquals($data, $this->helper->cutLongDescription($data));
    }

    /**
     * @covers BlockHelper::getAdditionalTitle
     */
    public function testGetAdditionalTitle()
    {
        $this->helper->expects($this->any())->method('getProductReviewUrl')->willReturn('test');
        $this->assertEquals('', $this->helper->getAdditionalTitle(0, 1));
        $this->assertEquals(
            ' with 5 stars |  <a rel="nofollow" title="Show All" href="test">Show All</a>',
            $this->helper->getAdditionalTitle(5, 1)
        );
    }
}
