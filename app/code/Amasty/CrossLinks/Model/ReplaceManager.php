<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model;

use \Magento\Catalog\Helper\Output;

/**
 * Class ReplaceManager
 * @package Amasty\CrossLinks\Model
 */
class ReplaceManager
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResourceModel\Link\CollectionFactory
     */
    protected $linksCollectionFactory;

    /**
     * @var \Amasty\CrossLinks\Helper\Data
     */
    protected $helper;

    /**
     * @var \Amasty\CrossLinks\Api\LinkInterface = null
     */
    protected $currentLink = null;

    /**
     * @var int
     */
    protected $replacementLimit = 0;

    /**
     * @var array
     */
    private $replacedJs = [];

    /**
     * @var array
     */
    private $replacedImg = [];

    /**
     * ReplaceManager constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Amasty\CrossLinks\Helper\Data $helper
     * @param ResourceModel\Link\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\CrossLinks\Helper\Data $helper,
        \Amasty\CrossLinks\Model\ResourceModel\Link\CollectionFactory $collectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->linksCollectionFactory = $collectionFactory;
        $this->prepareCollection();
    }

    /**
     * Prepare links collection singleton on a frontend
     *
     * @return $this
     */
    protected function prepareCollection()
    {
        $this->linksCollectionFactory->get()
            ->addStoreIdFilter([$this->storeManager->getStore()->getId(), \Magento\Store\Model\Store::DEFAULT_STORE_ID])
            ->addPriorityOrder()
            ->addStatusFilter()
            ->groupById();
        return $this;
    }

    /**
     * @param Output $helper
     * @param string $content
     * @param array $params
     * @return string
     */
    public function productAttribute(Output $helper, $content, $params)
    {
        if (!$this->isReplacementAllowed('product', $params['attribute'])) {
            return $content;
        }
        return $this->replaceLinks($content);
    }

    /**
     * @param Output $helper
     * @param string $content
     * @param array $params
     * @return $this
     */
    public function categoryAttribute(Output $helper, $content, $params)
    {
        if (!$this->isReplacementAllowed('category', $params['attribute'])) {
            return $content;
        }
        return $this->replaceLinks($content);
    }

    /**
     * @param string $content
     * @return string
     */
    public function processCmsPageContent($content)
    {
        return $this->replaceLinks($content);
    }

    /**
     * @param string $content
     * @return string
     */
    public function processFaqPageContent($content)
    {
        $this->setReplacementLimit($this->helper->getFaqReplacementLimit());
        return $this->replaceLinks($content);
    }

    /**
     * @param string $entity
     * @param $attribute
     * @return bool
     */
    protected function isReplacementAllowed($entity, $attribute)
    {
        $attributes = $this->helper->getEntityReplacementAttributeCodes($entity);
        return in_array($attribute, $attributes);
    }

    /**
     * @param int $replacementLimit
     */
    public function setReplacementLimit($replacementLimit)
    {
        $this->replacementLimit = $replacementLimit;
    }

    /**
     * @param $matches
     * @return mixed
     */
    protected function replace($matches)
    {
        if ($this->currentLink
            && $this->currentLink->getReplacementLimit() > 0
            && $this->replacementLimit > 0
            && empty($matches[1])
            && empty($matches[2])
            && empty($matches[3])
        ) {
            $this->currentLink->setReplacementLimit($this->currentLink->getReplacementLimit() - 1);
            $this->replacementLimit--;
            return $this->currentLink->getLinkHtml($matches[0]);
        }
        return $matches[0];
    }

    /**
     * @param $content
     * @return string
     */
    protected function replaceLinks($content)
    {
        if ($this->helper->isActive()) {
            $content = $this->removeScriptContent($content);
            $content = $this->removeImageContent($content);
            $linkCollection = $this->linksCollectionFactory->get();
            foreach ($linkCollection as $link) {
                $this->currentLink = $link;
                foreach ($link->getKeywords() as $keyword) {
                    $keyword = $this->prepareKeyword($keyword);
                    $content = preg_replace_callback("/(\<a[^\<]*)?{$keyword}/im", [$this, 'replace'], $content);
                }
            }
            $content = $this->returnScriptContent($content);
            $content = $this->returnImageContent($content);
        }
        return $content;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setEntityType($type)
    {
        $this->replacementLimit = $this->helper->getEntityReplacementLimit($type);
        return $this;
    }

    /**
     * Preparing keyword for search by regular expression
     *
     * @param string $keyword
     * @return string mixed
     */
    protected function prepareKeyword($keyword)
    {
        $rawKeyword = trim($keyword, '+');

        $enclosedRawKeyword = str_replace(
            ['\\', '+', '.', '/', '<', '>', '{', '}', '[', ']', '$', '^', '(', ')', '|', '*', '?'],
            ['\\\\' , '\+', '\.', '\/', '\<', '\>', '\{', '\}', '\[', '\]', '\$', '\^', '\(', '\)', '\|', '\*', '\?'],
            $rawKeyword
        );

        $keyword = str_replace($rawKeyword, $enclosedRawKeyword, $keyword);
        $regexpr = $this->helper->getAdvancedRegexpr();

        $keyword = (substr($keyword, 0, 1) === '+') ?
            substr_replace($keyword, $regexpr, 0,1)
            : '([a-zA-Z0-9]*)' . $keyword;

        $keyword = (substr($keyword, -1, 1) === '+') ?
            substr_replace($keyword, $regexpr, -1,1)
            : $keyword . '([a-zA-Z0-9]*)';

        return $keyword;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function removeScriptContent($content)
    {
        $content = preg_replace_callback(
            '#(\<script[^\>]*\>)(.*?)(\<\/script\>)#ims',
            [$this, 'replaceJS'],
            $content
        );

        return $content;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function removeImageContent($content)
    {
        $content = preg_replace_callback(
            '#(\<img)(.*?)(\/\>)#ims',
            [$this, 'replaceImage'],
            $content
        );

        return $content;
    }

    /**
     * @param array $matches
     * @return string
     */
    private function replaceJS($matches)
    {
        $text = '';
        if (count($matches) >= 4) {
            $this->replacedJs[] = $matches[2];
            $text = $matches[1] . '{{CROSS_LINK_' . (count($this->replacedJs) - 1) . '}}' . $matches[3];
        } elseif (isset($matches[0])) {
            $text = $matches[0];
        }

        return $text;
    }

    /**
     * @param array $matches
     * @return string
     */
    private function replaceImage($matches)
    {
        $text = '';
        if (count($matches) >= 4) {
            $this->replacedImg[] = $matches[2];
            $text = $matches[1] . '{{CROSS_LINK_IMG_' . (count($this->replacedImg) - 1) . '}}' . $matches[3];
        } elseif (isset($matches[0])) {
            $text = $matches[0];
        }

        return $text;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function returnScriptContent($content)
    {
        foreach ($this->replacedJs as $key => $js) {
            $content = str_replace('{{CROSS_LINK_' . $key . '}}', $js, $content);
        }

        return $content;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function returnImageContent($content)
    {
        foreach ($this->replacedImg as $key => $image) {
            $content = str_replace('{{CROSS_LINK_IMG_' . $key . '}}', $image, $content);
        }

        return $content;
    }
}
