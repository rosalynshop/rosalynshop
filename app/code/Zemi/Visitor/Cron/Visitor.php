<?php

namespace Zemi\Visitor\Cron;


use Magento\Framework\Escaper;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Visitor
{
    /**
     * @var \Zemi\Visitor\Helper\Data
     */
    protected $_helperData;

    /**
     * @var
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Event\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Visitor constructor.
     * @param \Zemi\Visitor\Helper\Data $helperData
     * @param RemoteAddress $remoteAddress
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Zemi\Visitor\Helper\Data $helperData,
        RemoteAddress $remoteAddress,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        SerializerInterface $serializer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_helperData = $helperData;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->serializer = $serializer;
        $this->_scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function execute()
    {
        if ($this->_helperData->visitorEnable()) {
            $viewed = [
                'date_time'      => date('Y-m-d H:i:s', strtotime('+7 hour', strtotime(gmdate('Y-m-d H:i:s')))),
                'count_visitor'  => count($this->_helperData->getVisitors()) ? : count($this->_helperData->getVisitorCustomerData()),
                'customer_data'  => $this->serializer->serialize($this->_helperData->getVisitorCustomerData()) ? : '',
            ];
            $parseDataVars = new \Magento\Framework\DataObject();
            $parseDataVars->setData($viewed);

            try {
                $this->inlineTranslation->suspend();
                $sender = [
                    'name' => $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'email' => $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                ];
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier('email_visitor_customer')
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(array('data' => $parseDataVars))
                    ->setFrom($sender)
                    ->addTo($this->_helperData->visitorEmailAdmin())
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }
}
