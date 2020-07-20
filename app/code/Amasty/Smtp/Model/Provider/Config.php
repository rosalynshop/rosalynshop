<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Model\Provider;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data;

class Config extends Data
{
    /**
     * @param \Magento\Framework\Config\ReaderInterface $reader
     * @param CacheInterface $cache
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $reader,
        CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, 'amsmtp_providers');
    }
}
