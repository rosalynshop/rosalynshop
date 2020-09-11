<?php

namespace MageBig\Ajaxwishlist\Controller\Wishlist;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \MageBig\Ajaxwishlist\Helper\Data
     */
    protected $_wishlistHelper;

    /**
     * @var null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * Add constructor.
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     * @param \Magento\Framework\Json\Helper\Data $jsonEncode
     * @param \MageBig\Ajaxwishlist\Helper\Data $wishlistHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator,
        \Magento\Framework\Json\Helper\Data $jsonEncode,
        \MageBig\Ajaxwishlist\Helper\Data $wishlistHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->_customerSession  = $customerSession;
        $this->wishlistProvider  = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->formKeyValidator  = $formKeyValidator;
        $this->_wishlistHelper   = $wishlistHelper;
        $this->_coreRegistry     = $registry;
        $this->_storeManager     = $storeManager;
        $this->jsonSerializer    = $jsonSerializer;
        parent::__construct($context);
    }

    public function execute()
    {
        $result     = [];
        $isLoggedIn = $this->_customerSession->isLoggedIn();

        if ($isLoggedIn == true) {
            try {
                $wishlist = $this->wishlistProvider->getWishlist();
                $session = $this->_customerSession;
                $requestParams = $this->getRequest()->getParams();

                if ($session->getBeforeWishlistRequest()) {
                    $requestParams = $session->getBeforeWishlistRequest();
                    $session->unsBeforeWishlistRequest();
                }

                $buyRequest = new \Magento\Framework\DataObject($requestParams);
                $product = $this->_initProduct();

                $this->_coreRegistry->register('product', $product);
                $this->_coreRegistry->register('current_product', $product);

                $resultItem = $wishlist->addNewItem($product, $buyRequest);
                if (is_string($resultItem)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($resultItem));
                }
                $wishlist->save();

                $this->_eventManager->dispatch(
                    'wishlist_add_product',
                    ['wishlist' => $wishlist, 'product' => $product, 'item' => $resultItem]
                );

                $wishlistHelper = $this->_objectManager->get('Magento\Wishlist\Helper\Data');
                $wishlistHelper->calculate();
                $itemCount = $wishlistHelper->getItemCount();

                $htmlPopup            = $this->_wishlistHelper->getSuccessHtml();
                $result['success']    = true;
                $result['item_count'] = $itemCount;
                $result['html_popup'] = $htmlPopup;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('You can\'t login right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $result['success'] = false;
            }
        } else {
            $product = $this->_initProduct();
            $this->_coreRegistry->register('product', $product);
            $this->_coreRegistry->register('current_product', $product);

            $htmlPopup            = $this->_wishlistHelper->getErrorHtml();
            $result['success']    = false;
            $result['html_popup'] = $htmlPopup;

        }
        $this->getResponse()->representJson(
            $this->jsonSerializer->serialize($result)
        );
    }

    /**
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     * @throws NoSuchEntityException
     */
    private function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
                return $product;
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
