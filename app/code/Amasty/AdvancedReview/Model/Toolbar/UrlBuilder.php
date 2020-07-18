<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Toolbar;

use Amasty\AdvancedReview\Model\Sources\Filter;

class UrlBuilder
{
    const MAGENTO_URL_PATH = 'review/product/listAjax';

    const DIRECTION_PARAM_NAME = 'amreview_dir';

    const SORT_PARAM_NAME = 'amreview_sort';

    const STARS_PARAM_NAME = 'stars';

    /**
     * @var array
     */
    protected $availableParams = [
        self::DIRECTION_PARAM_NAME,
        self::SORT_PARAM_NAME,
        self::STARS_PARAM_NAME,
        Filter::WITH_IMAGES,
        Filter::VERIFIED,
        Filter::RECOMMENDED
    ];

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $magentoUrlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\UrlInterface $magentoUrlBuilder,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->request = $request;
        $this->magentoUrlBuilder = $magentoUrlBuilder;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param string $type
     * @param string $value
     *
     * @return string
     */
    public function generateUrl($type, $value)
    {
        $params = $this->collectParams();
        if ($value === null) {
            unset($params[$type]);
        } else {
            $params[$type] = $value;
        }

        $params['_secure'] = $this->request->isSecure();
        if ($this->getProduct()) {
            $params['id'] = $this->getProduct()->getId();
        }

        return $this->magentoUrlBuilder->getUrl(
            self::MAGENTO_URL_PATH,
            $params
        );
    }

    /**
     * @return array
     */
    public function collectParams()
    {
        $result = [];
        foreach ($this->request->getParams() as $param => $value) {
            if (in_array($param, $this->availableParams)) {
                $result[$param] = $value;
            }
        }

        return $result;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }
}
