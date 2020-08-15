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
use Mirasvit\CacheWarmer\Service\Rate\ServerLoadRateService;

class Serverloadinfo extends AbstractComponent
{
    /**
     * @var FillRateService
     */
    private $fillServerService;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ServerLoadRateService  $fillServerService
     * @param Config                 $config
     * @param ContextInterface       $context
     * @param UiComponentInterface[] $components
     * @param array                  $data
     */
    public function __construct(
        ServerLoadRateService $fillServerService,
        Config $config,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->fillServerService = $fillServerService;
        $this->config            = $config;

        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
        return 'server_load';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        $config['fillServerHistory'] = $this->fillServerService->getHistory();

        $this->setData('config', $config);

        parent::prepare();
    }
}
