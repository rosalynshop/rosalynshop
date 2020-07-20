<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Model;

use Amasty\Smtp\Helper\Data;
use Amasty\Smtp\Model\Logger\DebugLogger;
use Amasty\Smtp\Model\Logger\MessageLogger;
use Amasty\Smtp\Plugin\Mail\Template\TransportBuilderByStorePlugin;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Zend\Http\Client\Adapter\Socket;

class Transport extends \Zend_Mail_Transport_Smtp implements TransportInterface
{
    const SOCK_AUTH_OPTIONS = [
        'timeout' => 20
    ];

    /**
     * @var MessageInterface|EmailMessageInterface
     */
    protected $_message;

    /**
     * @var MessageInterface
     */
    protected $_mailMessage;

    /**
     * @var MessageLogger
     */
    protected $messageLogger;

    /**
     * @var DebugLogger
     */
    protected $debugLogger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Socket
     */
    private $socket;

    public function __construct(
        $message,
        MessageInterface $mailMessage,
        MessageLogger $messageLogger,
        DebugLogger $debugLogger,
        Data $helper,
        ObjectManagerInterface $objectManager,
        Config $config,
        Registry $registry,
        Socket $socket,
        $host = \Amasty\Base\Model\GetCustomerIp::LOCAL_IP,
        array $parameters = []
    ) {
        parent::__construct($host, $parameters);
        $this->_message = $message;
        $this->_mailMessage = $mailMessage;
        $this->messageLogger = $messageLogger;
        $this->debugLogger = $debugLogger;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->registry = $registry;
        $this->socket = $socket;
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws MailException
     */
    public function sendMessage()
    {
        $this->debugLogger->log(__('Ready to send e-mail at amsmtp/transport::sendMessage()'));

        if ($this->_message->getBody()) {
            $logId = $this->messageLogger->log($this->_message);
        } else {
            $logId = $this->messageLogger->log($this->_mailMessage);
        }

        try {
            $storeId = $this->helper->getCurrentStore();

            if (!$this->config->getDisableDelivery($storeId)) {
                $this->setFromData(false, $storeId, ScopeInterface::SCOPE_STORE);

                if ($this->_message instanceof \Zend_Mail) {
                    $this->modifyMessage();

                    if ($this->_message->getBody()) {
                        parent::send($this->_message);
                    } else {
                        parent::send($this->_mailMessage);
                    }
                } else {
                    /** @var \Zend\Mail\Transport\Smtp $zendTransport */
                    $zendTransport = $this->objectManager->get(\Zend\Mail\Transport\Smtp::class);
                    $zendSmtpOptions = new \Zend\Mail\Transport\SmtpOptions(
                        [
                            'name' => $this->_name,
                            'host' => $this->_host,
                            'port' => $this->_port,
                            'connection_config' => $this->_config
                        ]
                    );

                    if ($this->_auth) {
                        $zendSmtpOptions->setConnectionClass($this->_auth);
                    }

                    $zendTransport->setOptions($zendSmtpOptions);
                    $zendTransport->send(
                        $this->modifyMessage(\Zend\Mail\Message::fromString($this->_message->getRawMessage()))
                    );
                }

                $this->debugLogger->log(__('E-mail sent successfully at amsmtp/transport::sendMessage().'));
            } else {
                $this->debugLogger->log(__('Actual delivery disabled under settings.'));
            }

            $this->messageLogger->updateStatus($logId, Log::STATUS_SENT);
        } catch (\Exception $e) {
            $this->debugLogger->log(__('Error sending e-mail: %1', $e->getMessage()));
            $this->messageLogger->updateStatus($logId, Log::STATUS_FAILED);

            throw new MailException(__($e->getMessage()));
        }
    }

    /**
     * @param $testEmail
     * @param $storeId
     * @param string $scope
     *
     * @throws MailException
     */
    public function runTest($testEmail, $storeId, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $this->checkConnection();

        if ($testEmail) {
            $this->setFromData(true, $storeId, $scope);
            $this->debugLogger->log(
                __(
                    'Preparing to send test e-mail to %1 from %2',
                    $testEmail,
                    $this->_config['custom_sender']['email']
                )
            );

            $this->_mailMessage->addTo($testEmail)
                ->setSubject((string)__('Amasty SMTP Email Test Message'))
                ->setBodyText((string)__('If you see this e-mail, your configuration is OK.'));

            try {
                $this->sendMessage();
                $this->debugLogger->log(__('Test e-mail was sent successfully!'));
            } catch (\Exception $e) {
                $this->debugLogger->log(__('Test e-mail failed: %1', $e->getMessage()));

                throw $e;
            }
        }
    }

    /**
     * @return \Zend\Mail\Message|MessageInterface
     */
    public function getMessage()
    {
        return $this->_mailMessage;
    }

    /**
     * @return $this
     */
    private function checkConnection()
    {
        $this->socket->setOptions(self::SOCK_AUTH_OPTIONS);

        try {
            $this->socket->connect($this->_host, $this->_port);
            $this->debugLogger->log(
                __(
                    'Connection test successful: connected to %1',
                    $this->_host . ':' . $this->_port
                )
            );
        } catch (\Exception $e) {
            $this->debugLogger->log(__($e->getMessage()));

            throw new MailException(__('Connection failed'));
        }

        return $this;
    }

    /**
     * @param null|\Zend\Mail\Message $message
     *
     * @return \Zend\Mail\Message|MessageInterface
     */
    private function modifyMessage($message = null)
    {
        if ($message) {
            $message->setSubject(htmlspecialchars_decode($message->getSubject()));
            $message->setEncoding('utf-8');
            $message->getHeaders()->removeHeader("Content-Disposition");
        }

        if (isset($this->_config['custom_sender'])) {
            return $this->setFrom(
                $this->_config['custom_sender']['email'],
                $this->_config['custom_sender']['name'],
                $message
            );
        } elseif ($message && !count($message->getFrom())
            && $this->registry->registry(TransportBuilderByStorePlugin::REGISTRY_KEY)
        ) {
            $defaultFrom = $this->registry->registry(TransportBuilderByStorePlugin::REGISTRY_KEY);

            return $this->setFrom(
                $defaultFrom['email'],
                $defaultFrom['name'],
                $message
            );
        }

        return $message;
    }

    /**
     * Set email sender
     * Function for compatibility of Zend Framework 1 and 2
     *
     * @param string $email
     * @param string $name
     * @param \Zend\Mail\Message|null $message
     *
     * @return MessageInterface|\Zend\Mail\Message|null
     */
    private function setFrom($email, $name, $message = null)
    {
        if (class_exists(\Zend\Mail\Message::class, false)
            && $message instanceof \Zend\Mail\Message
        ) {
            $message->setEncoding('utf-8');
            $message->setFrom($email, $name);

            return $message;
        } else {
            $this->_mailMessage->clearFrom();
            $this->_mailMessage->setFrom($email, $name);
        }

        return $this->getMessage();
    }

    /**
     * @param string $storeId
     * @param string $scope
     *
     * @throws MailException
     */
    private function setFromData($testEmail, $storeId, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        if ($this->config->getUseCustomSender($storeId) || $testEmail) {
            $from = $this->config->getCustomSender($storeId, $scope);

            if (!$from['email'] || !$from['name']) {
                $from = $this->config->getGeneralEmail($storeId, $scope);

                if (!$from['email'] || !$from['name']) {
                    throw new MailException(
                        __(
                            '\'Sender Email\' or \'Sender Name\' is empty. Please ensure that all values in the '
                            . '\'General Contact\' section are correctly set by visiting Stores > Configuration > '
                            . 'General > Store Email Addresses.'
                        )
                    );
                }
            }

            $this->_config['custom_sender']['email'] = $from['email'];
            $this->_config['custom_sender']['name'] = $from['name'];
        }
    }
}
