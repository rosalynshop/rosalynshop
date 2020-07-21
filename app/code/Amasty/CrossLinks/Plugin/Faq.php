<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Plugin;

use Amasty\CrossLinks\Helper\Data as CrossLinksHelper;

class Faq
{
    /**
     * @var \Amasty\CrossLinks\Model\ReplaceManager
     */
    private $replaceManager;

    /**
     * @var CrossLinksHelper
     */
    private $helper;

    public function __construct(
        CrossLinksHelper $helper,
        \Amasty\CrossLinks\Model\ReplaceManager $replaceManager
    ) {
        $this->replaceManager = $replaceManager;
        $this->helper = $helper;
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterWrapContent($subject, $result)
    {
        if ($result && $this->helper->isActiveForFaq()) {
            $result = $this->replaceManager->processFaqPageContent($result);
        }

        return $result;
    }
}
