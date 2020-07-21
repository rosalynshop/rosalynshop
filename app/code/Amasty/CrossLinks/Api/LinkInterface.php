<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Api;

/**
 * Abstract GiftCard Entrity Interface.
 */
interface LinkInterface
{
    /**
     * Set Link Title
     *
     * @param string $title
     * @return \Amasty\CrossLinks\Api\LinkInterface
     */
    public function setTitle($title);

    /**
     * Get Link Title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @return array
     */
    public function getKeywords();
}
