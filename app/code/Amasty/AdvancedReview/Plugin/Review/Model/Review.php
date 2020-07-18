<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Review\Model;

use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Helper\ImageHelper;
use Amasty\AdvancedReview\Model\Sources\Recommend;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Review\Model\Review as MagentoReview;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Review
 * @package Amasty\AdvancedReview\Plugin\Review\Model
 */
class Review
{
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $fileUploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\AdvancedReview\Model\ImagesFactory
     */
    private $imagesFactory;

    /**
     * @var \Amasty\AdvancedReview\Model\Repository\ImagesRepository
     */
    private $imagesRepository;

    /**
     * @var \Amasty\AdvancedReview\Model\ResourceModel\Sales\Item\CollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Amasty\AdvancedReview\Model\ResourceModel\Review
     */
    private $reviewResource;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Amasty\AdvancedReview\Model\Email\AdminNotificationSender
     */
    private $adminNotificationSender;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    public function __construct(
        Config $configHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Amasty\AdvancedReview\Model\ImagesFactory $imagesFactory,
        \Amasty\AdvancedReview\Model\Repository\ImagesRepository $imagesRepository,
        \Amasty\AdvancedReview\Model\ResourceModel\Sales\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Amasty\AdvancedReview\Model\ResourceModel\Review $reviewResource,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Amasty\AdvancedReview\Model\Email\AdminNotificationSender $adminNotificationSender,
        \Magento\Framework\App\State $appState
    ) {
        $this->configHelper = $configHelper;
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->ioFile = $ioFile;
        $this->logger = $logger;
        $this->imagesFactory = $imagesFactory;
        $this->imagesRepository = $imagesRepository;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->adapterFactory = $adapterFactory;
        $this->customerRepository = $customerRepository;
        $this->reviewResource = $reviewResource;
        $this->messageManager = $messageManager;
        $this->adminNotificationSender = $adminNotificationSender;
        $this->appState = $appState;
    }

    /**
     * @param MagentoReview $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterAggregate(
        MagentoReview $subject,
        $result
    ) {
        $this->uploadReviewImages($subject);
        $this->adminNotificationSender->notifyInstantly($subject);

        return $result;
    }

    /**
     * @param MagentoReview $subject
     */
    private function uploadReviewImages(MagentoReview $subject)
    {
        $files = $this->request->getFiles('review_images');
        $reviewId = $subject->getReviewId();
        if ($files && $reviewId && $this->configHelper->isAllowImages()) {
            foreach ($files as $fileId => $file) {
                if (UPLOAD_ERR_OK == $file['error']) {
                    $this->uploadImage($file, $reviewId);
                }
            }
        }
    }

    /**
     * @param MagentoReview $subject
     * @param $result
     * @return array|bool
     * @throws \Zend_Validate_Exception
     */
    public function afterValidate(
        MagentoReview $subject,
        $result
    ) {
        if (!$this->isFrontendArea()) {
            return $result;
        }

        if ($result === true) {
            $result = [];
        }

        if ($this->configHelper->isEmailFieldEnable()
            && $this->configHelper->isGDPREnabled()
            && !$this->request->getParam('gdpr', false)
        ) {
            $result[] = __('Please agree to the Privacy Policy');
        }

        if ($this->configHelper->isAllowImages()
            && $this->configHelper->isImagesRequired()
            && !$this->request->getFiles('review_images')
        ) {
            $result[] = __('Please enter review images.');
        }

        if (empty($result)) {
            return true;
        }

        return $result;
    }

    /**
     * @param $file
     * @param $reviewId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function uploadImage($file, $reviewId)
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            ImageHelper::IMAGE_PATH
        );
        $this->ioFile->checkAndCreateFolder($path);

        try {
            /** @var $uploader Uploader */
            $uploader = $this->fileUploaderFactory->create(
                ['fileId' => $file]
            );
            $imageAdapter = $this->adapterFactory->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($path);
            $this->trim($result);

            $this->saveImage($result, $reviewId);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while uploading image.'));
            throw new LocalizedException(__($e->getMessage()));
        }
        return $this;
    }

    /**
     * Fix for magento 2114 - setup upgrade
     * @param $result
     */
    private function trim($result)
    {
        if (isset($result['path']) && $result['file']) {
            $path = rtrim($result['path'], '/') . $result['file'];
            $this->ioFile->chmod($path, 0777);
        }
    }

    /**
     * @param $result
     * @param $reviewId
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function saveImage($result, $reviewId)
    {
        /** @var \Amasty\AdvancedReview\Model\Images $model */
        $model = $this->imagesFactory->create();
        $model->setReviewId($reviewId);
        $model->setPath($result['file']);

        $this->imagesRepository->save($model);
    }

    /**
     * @param MagentoReview $subject
     */
    public function beforeSave(MagentoReview $subject)
    {
        $recommend = (int)$this->request->getParam('is_recommended');
        if ($recommend) {
            $subject->setData('is_recommended', $recommend);
        } elseif ($this->configHelper->isRecommendFieldEnabled() && !$subject->getReviewId()) {
            $subject->setData('is_recommended', Recommend::NOT_RECOMMENDED);
        }

        $productId = $subject->getEntityPkValue();
        $customerId = $subject->getCustomerId();
        if ($customerId && !$subject->getReviewId()) {
            /** @var \Amasty\AdvancedReview\Model\ResourceModel\Sales\Item\Collection $collection */
            $collection = $this->itemCollectionFactory->create();

            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
                return;//customer was deleted
            }

            if ($collection->getProductItemCount((int)$productId, $customer->getEmail())) {
                $subject->setVerifiedBuyer(true);
            }
        }
    }

    /**
     * @param MagentoReview $subject
     * @param $object
     *
     * @return mixed
     */
    public function afterSave(MagentoReview $subject, $object)
    {
        if ($this->configHelper->isProsConsEnabled() || $this->configHelper->isEmailFieldEnable()) {
            $data = [];
            foreach (['like_about', 'not_like_about', 'guest_email'] as $item) {
                $value = $this->request->getParam($item, '');
                if ($value) {
                    $data[$item] = $value;
                }
            }

            if ($data) {
                try {
                    $this->reviewResource->insertAdditionalData($subject->getReviewId(), $data);
                } catch (\Exception $exc) {
                    $this->logger->critical($exc);
                }
            }
        }

        return $object;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    protected function isFrontendArea()
    {
        return $this->appState->getAreaCode() === 'frontend';
    }
}
