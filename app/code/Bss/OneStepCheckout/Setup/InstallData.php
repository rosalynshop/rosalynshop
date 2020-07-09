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
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallData
 *
 * @package Bss\OneStepCheckout\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute(
            Order::ENTITY,
            'delivery_date',
            [
                'type'     => Table::TYPE_DATETIME,
                'visible' => false,
                'required' => false
            ]
        )->addAttribute(
            Order::ENTITY,
            'delivery_comment',
            [
                'type'     => Table::TYPE_TEXT,
                'visible' => false,
                'required' => false
            ]
        );

        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute(
            'quote',
            'delivery_date',
            [
                'type'     => Table::TYPE_DATETIME,
                'visible' => false,
                'required' => false
            ]
        )->addAttribute(
            'quote',
            'delivery_comment',
            [
                'type'     => Table::TYPE_TEXT,
                'visible' => false,
                'required' => false
            ]
        );
    }
}
