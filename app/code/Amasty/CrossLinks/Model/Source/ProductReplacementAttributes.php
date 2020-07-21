<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;

/**
 * Class ProductReplacementAttributes
 * @package Amasty\CrossLinks\Model\Source
 */
class ProductReplacementAttributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var AttributeCollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var array
     */
    protected $allowedAttributeCodes = [];

    /**
     * ProductReplacementAttributes constructor.
     * @param AttributeCollectionFactory $collectionFactory
     * @param array $allowedAttributeCodes
     */
    public function __construct(
        AttributeCollectionFactory $collectionFactory,
        array $allowedAttributeCodes = []
    ) {
        $this->attributeCollectionFactory = $collectionFactory;
        $this->allowedAttributeCodes = $allowedAttributeCodes;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_getOptions() as $optionValue => $optionLabel) {
            $options[] = ['value' => $optionValue, 'label' => $optionLabel];
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_getOptions();
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $collection = $this->attributeCollectionFactory->create();
        $collection->addVisibleFilter()->removePriceFilter()->setCodeFilter($this->allowedAttributeCodes);

        $collection->addOrder('attribute_code', 'asc');
        $options = [];
        foreach ($collection->getItems() as $attribute) {
            /** @var Attribute $attribute */
            $options[$attribute->getAttributeCode()] = $attribute->getAttributeCode();
        }

        return $options;
    }
}
