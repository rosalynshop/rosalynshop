<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\HolePunch;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;

class HolePunchProcessor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockTagsMarker
     */
    private $blockTagsMarker;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param BlockFactory $blockFactory
     * @param BlockTagsMarker $blockTagsMarker
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param DesignInterface $design
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        BlockFactory $blockFactory,
        BlockTagsMarker $blockTagsMarker,
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        Registry $registry,
        StoreManagerInterface $storeManager,
        DesignInterface $design,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockTagsMarker = $blockTagsMarker;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->design = $design;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $containers
     * @param \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http $response
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\Response\Http
     */
    public function processPageCache($containers, $response)
    {
        $this->registerData($response);

        foreach ($containers as $container) {
            $container = $this->prepareContainer($container);
            $html = $this->generateBlockOutput($container);
            $response = $this->applyToContent($html, $response, $container);
        }

        return $response;
    }

    /**
     * @param \Magento\Framework\App\Response\Http $response
     */
    public function registerData($response)
    {
        if ($response->getHeader('am_prod')) {
            $productId = $response->getHeader('am_prod')->getFieldValue();
            $product = $this->loadProduct($productId);
            $this->registry->register('product', $product);
            $this->registry->register('current_product', $product);
        } elseif ($response->getHeader('am_cat')) {
            $categoryId = $response->getHeader('am_cat')->getFieldValue();
            $category = $this->loadCategory($categoryId);
            $this->registry->register('category', $category);
            $this->registry->register('current_category', $category);
        }
    }

    /**
     * @param int $id
     * @return \Magento\Catalog\Model\Product
     */
    private function loadProduct($id)
    {
        return $this->productRepository->getById($id, false, $this->storeManager->getStore()->getId());
    }

    /**
     * @param int $id
     * @return \Magento\Catalog\Model\Category
     */
    private function loadCategory($id)
    {
        return $this->categoryRepository->get($id, $this->storeManager->getStore()->getId());
    }

    /**
     * Get hash from tags and decode into block params array
     * @param string $container
     *
     * @return array|false
     */
    private function prepareContainer($container)
    {
        $container = $this->blockTagsMarker->processHash($container);
        $keys = $this->blockTagsMarker->getKeysArray();
        $containerArray = explode(BlockTagsMarker::SEPARATOR, $container);

        return array_combine($keys, $containerArray);
    }

    /**
     * @param array $container
     *
     * @return string|string[]|null
     */
    private function generateBlockOutput($container)
    {
        if ($container[BlockTagsMarker::MODULE_NAME] == 'Magento_Cms'
            && $container[BlockTagsMarker::CMS_WIDGET_ID]
        ) {
            $html = $this->generateBlock($container, true);
        } else {
            $html = $this->generateBlock($container, false);
        }
        $html = preg_replace('/<!--(.*)-->/', '', $html);

        return $html;
    }

    /**
     * @param array $container
     * @param bool $isCmsBlock
     *
     * @return string|string[]|null
     */
    private function generateBlock($container, $isCmsBlock)
    {
        $arguments = [];

        if (!empty($container[BlockTagsMarker::ARGUMENTS])) {
            $arguments = $this->prepareArguments($container[BlockTagsMarker::ARGUMENTS]);
        }

        if (!empty($container[BlockTagsMarker::TEMPLATE])) {
            $templateFile = $container[BlockTagsMarker::TEMPLATE];
        } else {
            $templateFile = $container[BlockTagsMarker::MODULE_NAME] . '::'
                . ltrim(mb_strrchr($container[BlockTagsMarker::TEMPLATE_FILE], '/', false), '/');
        }
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->blockFactory
            ->createBlock($container[BlockTagsMarker::BLOCK_CLASS], $arguments);

        if ($isCmsBlock) {
            $block->setBlockId($container[BlockTagsMarker::CMS_WIDGET_ID]);
        }
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
        $originalTheme = $this->design->getDesignTheme();
        $this->design->setDesignTheme($themeId);

        $block->setTemplate($templateFile);
        $html = $block->toHtml();
        $this->design->setDesignTheme($originalTheme);

        return $html;
    }

    /**
     * @param string $argumentsJson
     *
     * @return array
     */
    private function prepareArguments($argumentsJson)
    {
        $arguments = json_decode($argumentsJson, true);
        $this->restoreObjects($arguments);

        return $arguments;
    }

    /**
     * Resotre objects used for block creation
     * @param array $arguments
     */
    private function restoreObjects(&$arguments)
    {
        if (is_array($arguments)) {
            foreach($arguments as $key => &$value) {
                if (is_array($value) && !isset($value['is_object'])) {
                    $this->restoreObjects($value);
                } elseif (is_array($value) && isset($value['is_object']) && $value['class'] != '') {
                    $obj = $this->objectManager->create($value['class']);
                    $arguments[$key] = $obj;
                }
            }
        }
    }

    /**
     * @param string $html
     * @param \Magento\Framework\App\Response\Http $response
     * @param array $container
     *
     * @return \Magento\Framework\App\Response\Http
     */
    private function applyToContent($html, $response, $container)
    {
        $content = $response->getContent();

        $startTag = $this->blockTagsMarker->getStartTag($container);
        $endTag = $this->blockTagsMarker->getEndTag($container);
        $pattern = '/' . preg_quote($startTag, '/') . '(.*?)' . preg_quote($endTag, '/') . '/ims';

        $content = preg_replace($pattern, str_replace('$', '\\$', $html), $content, 1);
        $response->setContent($content);

        return $response;
    }
}
