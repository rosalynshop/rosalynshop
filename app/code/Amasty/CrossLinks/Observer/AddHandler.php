<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Observer;

use Magento\Framework\Event\ObserverInterface;
use Amasty\CrossLinks\Helper\Data as CrossLinksHelper;

/**
 * Class AddHandler
 * @package Amasty\CrossLinks\Observer
 */
class AddHandler implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $outputHelper;

    /**
     * @var \Amasty\CrossLinks\Model\ReplaceManager
     */
    protected $replaceManager;

    /**
     * AddHandler constructor.
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Amasty\CrossLinks\Model\ReplaceManager $replaceManager
     */
    public function __construct(
        \Magento\Catalog\Helper\Output $outputHelper,
        \Amasty\CrossLinks\Model\ReplaceManager $replaceManager
    ) {
        $this->outputHelper = $outputHelper;
        $this->replaceManager = $replaceManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        $product = $observer->getEvent()->getProduct();
        switch (true) {
            case !is_null($product) :
                $this->replaceManager->setEntityType(CrossLinksHelper::TYPE_PRODUCT);
                break;
            case !is_null($category) :
                $this->replaceManager->setEntityType(CrossLinksHelper::TYPE_CATEGORY);
                break;
        }
        $this->outputHelper->addHandler('productAttribute', $this->replaceManager);
        $this->outputHelper->addHandler('categoryAttribute', $this->replaceManager);
        return $this;
    }
}
