<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Email;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\Exception\NoSuchEntityException as NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Review\Model\Review as MagentoReview;

class Grid extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::email/reviews.phtml';

    /**
     * @var []
     */
    protected $reviews;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        Template\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->backendUrl = $backendUrl;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param MagentoReview|ProductModel $review
     *
     * @return string
     */
    public function getProductName($review)
    {
        if ($review->getSku() && $review->getName()) {
            $name = $review->getName() . ' (' . $review->getSku() . ')';
        } else {
            $productId = $review->getEntityPkValue();
            $name = 'N/A';
            if ($productId) {
                try {
                    $product = $this->productRepository->getById($productId, false, $review->getStoreId());
                    $name = $product->getName() . ' (' . $product->getSku() . ')';
                } catch (NoSuchEntityException $ex) {
                    $name = 'N/A';
                }
            }
        }

        return $name;
    }

    /**
     * @param MagentoReview|ProductModel $review
     *
     * @return string
     */
    public function getCustomerEmail($review)
    {
        if ($customerId = $review->getCustomerId()) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $email = $customer->getEmail();
            } catch (NoSuchEntityException $ex) {
                $email = '';
            }
        } else {
            $email = $review->getData('guest_email');
        }

        $email  = $email ? '(' . $email . ')' : '';

        return $email;
    }

    /**
     * @param MagentoReview|ProductModel $review
     *
     * @return string
     */
    public function getReviewUrl($review)
    {
        return $this->backendUrl->getUrl(
            'review/product/edit',
            [
                'id'        => $review->getReviewId(),
                '_secure'   => true,
                '_nosecret' => true
            ]
        );
    }

    /**
     * @return array
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param array $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }
}
