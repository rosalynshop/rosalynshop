<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SocialLogin
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SocialLoginPro\Block\Adminhtml\Account\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class Manager
 * @package Mageplaza\SocialLoginPro\Block\Adminhtml\Account\Edit\Tab
 */
class Manager extends Generic implements TabInterface
{
    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_apply_to';

    /**
     * @param string $html
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterToHtml($html)
    {
        $htmlLayout = $this->getLayout()
            ->createBlock('Mageplaza\SocialLoginPro\Block\Adminhtml\Account\Edit\Tab\Grid\Grid')
            ->toHtml();
        if (!$htmlLayout) {
            return "";
        }

        $html = "<div class='fieldset-wrapper-title'><strong class='title'><span>" . __('Social Login Manager') . "</span></strong></div>";
        $html .= $htmlLayout;

        return $html;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Template Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Template Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
