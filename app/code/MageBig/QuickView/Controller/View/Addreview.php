<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\QuickView\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Addreview extends Action
{
    public function __construct(
        Context $context,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_reviewFactory            = $reviewFactory;
        $this->_ratingFactory            = $ratingFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager             = $storeManager;
    }

    public function execute()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility([2, 4]);

        $numberOfItems = 6;
        $candidateIds  = $collection->getAllIds();
        // $candidateIds  = [59,60,61,62,63,64,65,66,67,68,69,70,71,72];
        $chosenIds     = [];
        $maxKey        = count($candidateIds) - 10;
        while (count($chosenIds) < $numberOfItems) {
            $randomKey             = mt_rand(0, $maxKey);
            $chosenIds[$randomKey] = $candidateIds[$randomKey];
        }
         $collection->addIdFilter($chosenIds);
//        $collection->addIdFilter($candidateIds);

        foreach ($collection as $product) {
            $productId                     = $product->getId();
            $reviewFinalData['ratings'][1] = 4;
            $reviewFinalData['ratings'][2] = 14;
            $reviewFinalData['ratings'][3] = 14;
            $reviewFinalData['nickname']   = "Liuli";
            $reviewFinalData['title']      = "Best Theme";
            $reviewFinalData['detail']     = "Martfury is a modern and flexible eCommerce Magento 2 theme. This theme is suited for marketplace, electronics store, furniture store, clothing store, hitech store and accessories store… With the theme, you can create easily and quickly your own store. -- Creating product reviews programmatically.";
            $review                        = $this->_reviewFactory->create()->setData($reviewFinalData);
            $review->unsetData('review_id');
            $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
                ->setEntityPkValue($productId)
                ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)//By default set approved
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->setStores([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])
                ->save();

            foreach ($reviewFinalData['ratings'] as $ratingId => $optionId) {
                $this->_ratingFactory->create()
                    ->setRatingId($ratingId)
                    ->setReviewId($review->getId())
                    ->addOptionVote($optionId, $productId);
            }

            $review->aggregate();
        }

        echo 'done';
    }
}