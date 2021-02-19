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

namespace Mageplaza\SocialLoginPro\Block\Adminhtml\Account\Edit\Tab\Grid;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;

/**
 * Class Grid
 * @package Mageplaza\SocialLoginPro\Block\Adminhtml\Account\Edit\Tab\Grid
 */
class Grid extends Extended
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var  CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Grid constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('social_customer_id');
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->getReport('mageplaza_socialloginpro_manager_listing_data_source')
            ->addFieldToSelect('social_customer_id')
            ->addFieldToSelect('social_id')
            ->addFieldToSelect('is_send_password_email')
            ->addFieldToSelect('type')
            ->addFieldToFilter('customer_id', $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));

        if ($collection->getData()) {
            $this->setCollection($collection);
        }

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('type', [
            'header' => __('Social'),
            'index'  => 'type',
            'type'   => 'text'
        ]);

        $this->addColumn('social_id', [
            'header' => __('Social Id'),
            'index'  => 'social_id'
        ]);
        $this->addColumn('is_send_password_email', [
            'header'   => __('Is Send Password Email'),
            'sortable' => false,
            'index'    => 'is_send_password_email',
            'type'     => 'options',
            'options'  => [
                '1' => __('Yes'),
                '0' => __('No')
            ]
        ]);

        return parent::_prepareColumns();
    }
}
