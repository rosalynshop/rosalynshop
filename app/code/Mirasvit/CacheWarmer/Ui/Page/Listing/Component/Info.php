<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Ui\Page\Listing\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\Rate\CacheCoverageRateService;
use Mirasvit\CacheWarmer\Service\Rate\CacheFillRateService;

class Info extends AbstractComponent
{
    /**
     * @var CacheFillRateService
     */
    private $fillRateService;

    /**
     * @var CacheCoverageRateService
     */
    private $coverageRateService;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        CacheFillRateService $fillRateService,
        CacheCoverageRateService $coverageRateService,
        Config $config,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->fillRateService     = $fillRateService;
        $this->coverageRateService = $coverageRateService;
        $this->config              = $config;

        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
        return 'fill_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        switch ($this->config->getCacheType()) {
            case 1:
                $cacheType = 'Built-in';
                break;
            case 'LITEMAGE':
                $cacheType = 'LiteMage';
                break;
            default:
                $cacheType = 'Varnish';
        }

        $config['cacheType']    = $cacheType;
        $config['cacheTtl']     = $this->prettifyTTL($this->config->getCacheTtl());
        $config['fillHistory']  = $this->getFillHistory();//$this->fillRateService->getHistory();
        $config['fillRates']    = [
            'inCache' => $this->fillRateService->getRate(),
            'total'   => 100,
        ];
        $config['coverageRate'] = $this->coverageRateService->getRate();

        $this->setData('config', $config);

        parent::prepare();
    }

    private function getFillHistory()
    {
        $history = [];

        $ts          = ceil($this->config->getDateTime()->getTimestamp() / 60) * 60 - 24 * 60 * 60;
        $rateHistory = $this->fillRateService->getHistory();

        for ($i = 0; $i < 24 * 60; $i++) {
            $ts += 60;

            $key = date("H:i", $ts);

            $history[$key] = isset($rateHistory[$ts]) ? $rateHistory[$ts] : 0;
        }

        return $history;
    }

    private function prettifyTTL($ttl)
    {
        $hour = 60 * 60;
        $day  = 24 * $hour;

        if ($ttl > 2 * $day) { // 2 days
            return __("%1 days", intval($ttl / $day));
        }

        if ($ttl > 3 * $hour) { // 3 hours
            return __("%1 hours", intval($ttl / $hour));
        }

        return __("%1 mins", intval($ttl / 60));
    }
}
