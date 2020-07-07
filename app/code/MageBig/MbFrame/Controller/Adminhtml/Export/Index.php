<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\MbFrame\Controller\Adminhtml\Export;

class Index extends \Magento\Catalog\Controller\Product
{
    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $_widgetFactory;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    protected $pageCollection;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection
     */
    protected $blockCollection;

    /**
     * @var \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection
     */
    protected $widgetCollection;

    /**
     * @var \MageBig\MbFrame\Setup\Model\Page
     */
    protected $pageSetup;

    /**
     * @var \MageBig\MbFrame\Setup\Model\Block
     */
    protected $blockSetup;

    /**
     * @var \MageBig\MbFrame\Setup\Model\Widget
     */
    protected $widgetSetup;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context                          $context
     * @param \Magento\Cms\Model\ResourceModel\Page\Collection               $pageCollection
     * @param \Magento\Cms\Model\ResourceModel\Block\Collection              $blockCollection
     * @param \Magento\Cms\Model\Block                                       $block
     * @param \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection $widgetCollection
     * @param \Magento\Widget\Model\Widget\InstanceFactory                   $widgetFactory
     * @param \MageBig\MbFrame\Setup\Model\Page                              $pageSetup
     * @param \MageBig\MbFrame\Setup\Model\Block                             $blockSetup
     * @param \MageBig\MbFrame\Setup\Model\Widget                            $widgetSetup
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Cms\Model\ResourceModel\Page\Collection $pageCollection,
        \Magento\Cms\Model\ResourceModel\Block\Collection $blockCollection,
        \Magento\Cms\Model\Block $block,
        \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection $widgetCollection,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \MageBig\MbFrame\Setup\Model\Page $pageSetup,
        \MageBig\MbFrame\Setup\Model\Block $blockSetup,
        \MageBig\MbFrame\Setup\Model\Widget $widgetSetup
    ) {
        $this->_widgetFactory   = $widgetFactory;
        $this->pageCollection   = $pageCollection;
        $this->blockCollection  = $blockCollection;
        $this->widgetCollection = $widgetCollection;
        $this->pageSetup        = $pageSetup;
        $this->blockSetup       = $blockSetup;
        $this->widgetSetup      = $widgetSetup;
        parent::__construct($context);
    }

    public function execute()
    {
        $dir = dirname(dirname(dirname(__DIR__))) . '/datacms';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $this->pageSetup->export();
        $this->blockSetup->export();
        $this->widgetSetup->export();
        echo 'done';
        exit;
    }
}
