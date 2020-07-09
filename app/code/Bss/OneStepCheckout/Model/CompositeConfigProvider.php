<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_OneStepCheckout
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\GiftMessage\Model\CompositeConfigProvider as GiftMessageConfig;
use Bss\OneStepCheckout\Helper\Config;

/**
 * Class CompositeConfigProvider
 *
 * @package Bss\OneStepCheckout\Model
 */
class CompositeConfigProvider implements ConfigProviderInterface
{
    /**
     * OSC config helper.
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\Json\Encoder
     */
    private $jsonEncoder;

    /**
     * Initialize dependencies.
     *
     * @param Config $configHelper
     * @param GiftMessageConfig $configProvider
     */
    public function __construct(
        Config $configHelper,
        GiftMessageConfig $configProvider
    ) {
        $this->configHelper = $configHelper;
        $this->configProvider = $configProvider;
    }

    /**
     * Append o config data.
     *
     * @return array
     */
    public function getConfig()
    {
        $output = [];
        $helper = $this->configHelper;
        if ($helper->isEnabled()) {
            $config = [];
            if ($helper->isDisplayField('enable_gift_message')) {
                $config['giftOptionsConfig'] = $this->getGiftOptionsConfigJson();
            }
            if ($api = $helper->getAutoCompleteGroup('google_api_key')) {
                $config['googleApi'] = $api;
            }
            if ($helper->getAutoCompleteGroup('allowspecific')) {
                $countries = explode(',', $helper->getAutoCompleteGroup('specificcountry'));
                $config['specificcountry'] = $countries;
            }
            $output['bssOsc'] = $config;
        }
        return $output;
    }

    /**
     * Retrieve gift message configuration
     *
     * @return string
     */
    private function getGiftOptionsConfigJson()
    {
        return $this->configProvider->getConfig();
    }
}
