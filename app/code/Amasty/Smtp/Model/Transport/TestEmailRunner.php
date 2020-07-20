<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Model\Transport;

use Amasty\Smtp\Model\Logger\DebugLogger;
use Amasty\Smtp\Model\Logger\MessageLogger;
use Magento\Framework\Mail\MessageInterface;
use Zend\Http\Client\Adapter\Socket;

class TestEmailRunner extends \Amasty\Smtp\Model\Transport
{
    /**
     * @var MessageInterface
     */
    protected $_message;

    /**
     * @var MessageInterface
     */
    protected $_mailMessage;

    /**
     * @var \Amasty\Smtp\Model\Logger\MessageLogger
     */
    protected $messageLogger;

    /**
     * @var \Amasty\Smtp\Model\Logger\DebugLogger
     */
    protected $debugLogger;

    /**
     * @var \Amasty\Smtp\Helper\Data
     */
    protected $helper;

    /**
     * @var \Amasty\Smtp\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var Socket
     */
    private $socket;

    public function __construct(
        MessageInterface $message,
        MessageInterface $mailMessage,
        MessageLogger $messageLogger,
        DebugLogger $debugLogger,
        \Amasty\Smtp\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Smtp\Model\Config $config,
        \Magento\Framework\Registry $registry,
        Socket $socket,
        $host = '127.0.0.1',
        array $parameters = []
    )
    {
        parent::__construct($message, $mailMessage, $messageLogger, $debugLogger, $helper, $objectManager, $config, $registry, $socket, $host, $parameters);
    }
}