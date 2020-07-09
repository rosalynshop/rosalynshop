<?php

namespace RosalynShop\Productslider\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Slider extends Template implements BlockInterface
{
    protected $_template = "widget/slider.phtml";
    public $_storeManager;

    /**
     * Slider constructor.
     * @param Template\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = [])
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getMediaUrl()
    {
        return $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }
}