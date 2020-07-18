<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Review\Block\Adminhtml;

class GridPlugin
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Magento\Review\Block\Adminhtml\Grid $subject
     *
     * @return array
     */
    public function beforeGetMassactionBlockHtml(\Magento\Review\Block\Adminhtml\Grid $subject)
    {
        $massBlock = $subject->getMassactionBlock();
        if ($massBlock) {
            $massBlock->addItem(
                'all_approve',
                [
                    'label' => __('Approve for All Store Views'),
                    'url' => $this->getUrl(
                        'amasty_advancedreview/review/massAllApprove',
                        ['ret' => $this->registry->registry('usePendingFilter') ? 'pending' : 'index']
                    ),
                    'confirm' => __('Are you sure?')
                ]
            );
        }
        return [];
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
