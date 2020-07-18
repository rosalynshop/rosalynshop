<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Ui\DataProvider\Form;

use Amasty\AdvancedReview\Api\CommentRepositoryInterface;
use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Amasty\AdvancedReview\Model\RegistryConstants;
use Amasty\AdvancedReview\Model\ResourceModel\Comment\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class CommentDataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        CommentRepositoryInterface $commentRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();

        if ($data['totalRecords'] > 0) {
            $commentId = (int)$data['items'][0][CommentInterface::ID];
            $model = $this->commentRepository->getById($commentId);
            $data[$commentId] = $model->getData();
        }

        if ($savedData = $this->dataPersistor->get(RegistryConstants::COMMENT_DATA)) {
            $savedCommentId = $savedData[CommentInterface::ID] ?? null;
            $data[$savedCommentId] = isset($data[$savedCommentId])
                ? array_merge($data[$savedCommentId], $savedData)
                : $savedData;
            $this->dataPersistor->clear(RegistryConstants::COMMENT_DATA);
        }

        return $data;
    }
}
