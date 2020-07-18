<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Repository;

use Amasty\AdvancedReview\Api\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Amasty\AdvancedReview\Model\ResourceModel;
use Amasty\AdvancedReview\Model\VoteFactory;

/**
 * Class VoteRepository
 * @package Amasty\AdvancedReview\Model\Repository
 */
class VoteRepository implements \Amasty\AdvancedReview\Api\VoteRepositoryInterface
{
    /**
     * @var array
     */
    private $vote = [];
    
    /**
     * @var ResourceModel\Vote
     */
    private $voteResource;

    /**
     * @var VoteFactory
     */
    private $voteFactory;

    /**
     * @var ResourceModel\Vote\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\AdvancedReview\Model\ResourceModel\Vote $voteResource,
        \Amasty\AdvancedReview\Model\VoteFactory $voteFactory,
        \Amasty\AdvancedReview\Model\ResourceModel\Vote\CollectionFactory $collectionFactory
    ) {
        $this->voteResource = $voteResource;
        $this->voteFactory = $voteFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\VoteInterface $vote)
    {
        if ($vote->getVoteId()) {
            $vote = $this->get($vote->getVoteId())->addData($vote->getData());
        }

        try {
            $this->voteResource->save($vote);
            $this->vote[$vote->getVoteId()] = $vote;
        } catch (\Exception $e) {
            if ($vote->getVoteId()) {
                throw new CouldNotSaveException(
                    __('Unable to save vote with ID %1. Error: %2', [$vote->getVoteId(), $e->getMessage()])
                );
            }
            throw new CouldNotSaveException(__('Unable to save new vote. Error: %1', $e->getMessage()));
        }
        
        return $vote;
    }

    /**
     * {@inheritdoc}
     */
    public function get($voteId)
    {
        if (!isset($this->vote[$voteId])) {
            /** @var \Amasty\AdvancedReview\Model\Vote $vote */
            $vote = $this->voteFactory->create();
            $this->voteResource->load($vote, $voteId);
            if (!$vote->getVoteId()) {
                throw new NoSuchEntityException(__('Vote with specified ID "%1" not found.', $voteId));
            }
            $this->vote[$voteId] = $vote;
        }

        return $this->vote[$voteId];
    }

    /**
     * @param $reviewId
     * @param $ip
     * @return \Amasty\AdvancedReview\Model\Vote|\Magento\Framework\DataObject
     */
    public function getByIdAndIp($reviewId, $ip)
    {
        /** @var \Amasty\AdvancedReview\Model\Vote $vote */
        $vote = $this->voteFactory->create();

        $collection = $this->collectionFactory->create()
                ->addFieldToFilter('review_id', $reviewId)
                ->addFieldToFilter('ip', $ip);
        $collection->getSelect()->limit(1);

        if ($collection->getSize() > 0) {
            $vote = $collection->getFirstItem();
        }

        return $vote;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\VoteInterface $vote)
    {
        try {
            $this->voteResource->delete($vote);
            unset($this->vote[$vote->getId()]);
        } catch (\Exception $e) {
            if ($vote->getVoteId()) {
                throw new CouldNotDeleteException(
                    __('Unable to remove vote with ID %1. Error: %2', [$vote->getVoteId(), $e->getMessage()])
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove vote. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($voteId)
    {
        $model = $this->get($voteId);
        $this->delete($model);
        return true;
    }

    /**
     * @param $reviewId
     * @param null $ip
     * @return array
     */
    public function getVotesCount($reviewId, $ip = null)
    {
        $result = [
            'plus' => 0,
            'minus' => 0
        ];

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('review_id', $reviewId);
        if ($ip) {
            $collection->addFieldToFilter('ip', $ip);
        }

        foreach ($collection as $vote) {
            if ($vote->getType() == '1') {
                $result['plus'] = ++$result['plus'];
            } else {
                $result['minus'] = ++$result['minus'];
            }
        }

        return $result;
    }

    /**
     * @return \Amasty\AdvancedReview\Model\Vote
     */
    public function getVoteModel()
    {
        return $this->voteFactory->create();
    }

    /**
     * @return array
     */
    public function getVoteIpKeys()
    {
        return $this->collectionFactory->create()->getVoteIpKeys();
    }
}
