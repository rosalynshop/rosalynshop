<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Plugin\Review\Model;

use Amasty\AdvancedReview\Plugin\Review\Model\Review;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Review\Model\Review as MagentoReview;

/**
 * Class ReviewTest
 *
 * @see Review
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ReviewTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    const TEST_FILE = [
        'fileId' => [
            'file' => 'test.txt',
            'error' => false
        ]
    ];

    /** @var Review|MockObject $review */
    private $review;

    /** @var MagentoReview|MockObject $subject */
    private $subject;

    /** @var \Magento\Framework\App\RequestInterface|MockObject $request */
    private $request;

    /** @var \Amasty\AdvancedReview\Helper\Config|MockObject $configHelper */
    private $configHelper;

    public function setUp()
    {
        $this->review = $this->getMockBuilder(Review::class)
            ->disableOriginalConstructor()
            ->setMethods(['uploadImage', 'isFrontendArea'])
            ->getMock();
        $this->subject = $this->getMockBuilder(MagentoReview::class)
            ->disableOriginalConstructor()
            ->setMethods(['getReviewId'])
            ->getMock();
        $this->subject->expects($this->any())->method('getReviewId')->willReturn(1);
        $this->review->expects($this->any())->method('uploadImage')->willReturn($this->review);

        $this->request = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFiles'])
            ->getMockForAbstractClass();

        $this->configHelper = $this->createMock(\Amasty\AdvancedReview\Helper\Config::class);
        $this->configHelper->expects($this->any())->method('isAllowImages')->willReturn(true);

        $this->setProperty($this->review, 'request', $this->request, Review::class);
        $this->setProperty($this->review, 'configHelper', $this->configHelper, Review::class);
    }

    /**
     * @covers Review::uploadReviewImages
     *
     * @throws \ReflectionException
     */
    public function testUploadReviewImages()
    {
        $this->request->expects($this->any())->method('getFiles')->with('review_images')->willReturn(self::TEST_FILE);

        $this->review->expects($this->once())->method('uploadImage');
        $this->invokeMethod($this->review, 'uploadReviewImages', [$this->subject]);
    }

    /**
     * @covers Review::afterValidate
     *
     * @dataProvider afterValidateDataProvider
     *
     * @throws \ReflectionException
     */
    public function testAfterValidate($file, $gdpr, $result, $expectedResult)
    {
        $this->configHelper->expects($this->any())->method('isEmailFieldEnable')->willReturn(true);
        $this->configHelper->expects($this->any())->method('isGDPREnabled')->willReturn(true);
        $this->configHelper->expects($this->any())->method('isImagesRequired')->willReturn(true);

        $this->request->expects($this->any())->method('getParam')->with('gdpr')->willReturn($gdpr);
        $this->request->expects($this->any())->method('getFiles')->with('review_images')->willReturn($file);

        $this->review->expects($this->any())->method('isFrontendArea')->willReturn(1);

        $this->assertEquals($expectedResult, $this->review->afterValidate($this->subject, $result));
    }

    /**
     * Data provider for afterValidate test
     * @return array
     */
    public function afterValidateDataProvider()
    {
        $errors = [
            'wrongCondition' =>
                [
                    __('Please agree to the Privacy Policy')
                ],
            'emptyImage' =>
                [
                    __('Please enter review images.')
                ],
            'summaryErrors' =>
                [
                    __('Please agree to the Privacy Policy'),
                    __('Please enter review images.')
                ]
        ];

        return [
            [self::TEST_FILE, false, true, $errors['wrongCondition']],
            [[], true, true, $errors['emptyImage']],
            [[], false, true, $errors['summaryErrors']],
            [self::TEST_FILE, true, false, true]
        ];
    }
}
