<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Plugin\Catalog\Block\Product;

class Image
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\Data $data
    ) {
        $this->data = $data;
    }

    /**
     * @param $subject
     * @param $proceed
     * @param string $key
     * @param null $index
     *
     * @return mixed
     */
    public function aroundGetData(
        $subject,
        $proceed,
        $key = '',
        $index = null
    ) {
        if ($key == 'label') {
            $imageAlt = $this->data->getReplaceData('image_alt');
            if ($imageAlt) {
                $data = $imageAlt;
            }
        }
        if (!isset($data)) {
            $data = $proceed($key, $index);
        }
        
        return $data;
    }
}
