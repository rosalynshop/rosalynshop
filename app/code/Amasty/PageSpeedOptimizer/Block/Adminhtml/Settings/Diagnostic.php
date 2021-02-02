<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\Url;

class Diagnostic extends Field
{
    private $urlBuilder;

    /**
     * @var string
     */
    protected $_template = 'Amasty_PageSpeedOptimizer::diagnostic.phtml';

    public function __construct(
        LocaleResolver $localeResolver,
        Template\Context $context,
        Url $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->setLocale($localeResolver->getLocale());
    }

    /**
     * @return string
     */
    public function getFrontendUrl()
    {
        if ($storeId = $this->getRequest()->getParam('store')) {
            $url = $this->urlBuilder->getUrl(null, ['_scope' => $storeId]);
        } else {
            $url = parent::getBaseUrl();
        }

        return $url;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }
}
