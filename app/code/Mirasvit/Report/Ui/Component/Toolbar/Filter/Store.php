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
 * @package   mirasvit/module-report
 * @version   1.3.44
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Ui\Component\Toolbar\Filter;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\AbstractComponent;

class Store extends AbstractComponent
{
    /**
     * @var string
     */
    private $columnName;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        $column,
        StoreManagerInterface $storeManager,
        ContextInterface $context,
        $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->columnName = $column;

        parent::__construct($context, $components, $data);
    }

    public function getComponentName()
    {
        return 'toolbar_filter_store';
    }

    public function prepare()
    {
        $storeIds = [];
        foreach ($this->storeManager->getStores() as $store) {
            $storeIds[] = $store->getId();
        }
        $options = [[
            'label'    => __('All Store Views'),
            'type'     => 'all',
            'storeIds' => implode(',', $storeIds),
        ]];

        $websites = $this->storeManager->getWebsites();

        foreach ($websites as $website) {
            $options[] = [
                'label'    => $website->getName(),
                'type'     => 'website',
                'storeIds' => implode(',', $website->getStoreIds()),
            ];

            /** @var \Magento\Store\Model\Group $group */
            foreach ($website->getGroups() as $group) {
                $options[] = [
                    'label'    => $group->getName(),
                    'type'     => 'group',
                    'storeIds' => implode(',', $group->getStoreIds()),
                ];

                /** @var \Magento\Store\Model\Store $store */
                foreach ($group->getStores() as $store) {
                    $options[] = [
                        'label'    => $store->getName(),
                        'type'     => 'store',
                        'storeIds' => $store->getId(),
                    ];
                }
            }
        }

        $config = $this->getData('config');

        $config['column'] = $this->columnName;
        $config['current'] = __('All Store Views');
        $config['stores'] = $options;

        $this->setData('config', $config);
    }
}