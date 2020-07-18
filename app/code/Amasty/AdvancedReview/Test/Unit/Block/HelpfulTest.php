<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Test\Unit\Block;

use Amasty\AdvancedReview\Block\Helpful;
use Amasty\AdvancedReview\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class HelpfulTest
 *
 * @see Helpful
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class HelpfulTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    const VOTES_COUNT_EMPTY = [
        'plus' => 0,
        'minus' => 0
    ];

    const VOTES_COUNT = [
        'plus' => 5,
        'minus' => 5
    ];

    /**
     * @var Helpful
     */
    private $block;

    /**
     * @var \Amasty\AdvancedReview\Model\Repository\VoteRepository
     */
    private $voteRepository;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    public function setUp()
    {
        $this->block = $this->getObjectManager()->getObject(Helpful::class);

        $this->voteRepository = $this->getMockBuilder(\Amasty\AdvancedReview\Model\Repository\VoteRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getVotesCount', 'getId'])
            ->getMock();

        $this->remoteAddress = $this->getMockBuilder(\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRemoteAddress'])
            ->getMock();

        $this->remoteAddress->expects($this->any())->method('getRemoteAddress')->willReturn(1);

        $this->voteRepository->expects($this->any())->method('getId')->willReturn(1);
        $this->block->setData('review', $this->voteRepository);

        $this->setProperty($this->block, 'voteRepository', $this->voteRepository, Helpful::class);
        $this->setProperty($this->block, 'remoteAddress', $this->remoteAddress, Helpful::class);
    }

    /**
     * @covers Helpful::getVotedByIp
     *
     * @throws \ReflectionException
     */
    public function testGetVotedByIp()
    {
        $this->voteRepository->expects($this->any())->method('getVotesCount')->willReturn(self::VOTES_COUNT);

        $result = $this->invokeMethod($this->block, 'getVotedByIp');

        $this->assertEquals(self::VOTES_COUNT, $result);
        $this->assertArrayHasKey('plus', $result);
        $this->assertArrayHasKey('minus', $result);

        $this->setProperty($this->block, 'voteByCurrentIp', self::VOTES_COUNT_EMPTY, Helpful::class);
        $this->assertEquals(self::VOTES_COUNT_EMPTY, $this->invokeMethod($this->block, 'getVotedByIp'));
    }

    /**
     * @covers Helpful::getVote
     *
     * @throws \ReflectionException
     */
    public function testGetVote()
    {
        $this->voteRepository->expects($this->any())->method('getVotesCount')->willReturn(self::VOTES_COUNT);

        $this->assertEquals(self::VOTES_COUNT, $this->invokeMethod($this->block, 'getVote'));
        $this->setProperty($this->block, 'voteByCurrentIp', self::VOTES_COUNT_EMPTY, Helpful::class);
        $this->assertEquals(self::VOTES_COUNT_EMPTY, $this->invokeMethod($this->block, 'getVotedByIp'));
    }

    /**
     * @covers Helpful::getPlusVotedClass
     *
     * @dataProvider getPlusVotedClassDataProvider
     *
     * @throws \ReflectionException
     */
    public function testGetPlusVotedClass($value, $expectedResult)
    {
        $this->voteRepository->expects($this->any())->method('getVotesCount')->willReturn($value);

        $result = $this->block->getPlusVotedClass();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for getPlusVotedClass test
     * @return array
     */
    public function getPlusVotedClassDataProvider()
    {
        return [
          [self::VOTES_COUNT, Helpful::VOTED_CLASS_NAME],
          [self::VOTES_COUNT_EMPTY, '']
        ];
    }

    /**
     * @covers Helpful::GetMinusVotedClass
     *
     * @dataProvider getMinusVotedClassDataProvider
     *
     * @throws \ReflectionException
     */
    public function testGetMinusVotedClass($value, $expectedResult)
    {
        $this->voteRepository->expects($this->any())->method('getVotesCount')->willReturn($value);

        $result = $this->block->getMinusVotedClass();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for getMinusVotedClass test
     * @return array
     */
    public function getMinusVotedClassDataProvider()
    {
        return [
            [self::VOTES_COUNT, Helpful::VOTED_CLASS_NAME],
            [self::VOTES_COUNT_EMPTY, '']
        ];
    }
}
