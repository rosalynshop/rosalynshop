<?php

namespace RosalynShop\RebuildUrl\Console\Command;

use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildUrl extends Command
{
    /**
     * @var AppState
     */
    protected $_appState;

    /**
     * @var \RosalynShop\RebuildUrl\Helper\Data
     */
    protected $helperData;

    /**
     * Product url factory
     *
     * @var \RosalynShop\RebuildUrl\Model\Product\UrlFactory
     */
    protected $_productUrlFactory;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * GenerateCommand constructor.
     * @param AppState $appState
     * @param \RosalynShop\RebuildUrl\Helper\Data $helperData
     * @param \RosalynShop\RebuildUrl\Model\Product\UrlFactory $productUrlFactory
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        AppState $appState,
        \RosalynShop\RebuildUrl\Helper\Data $helperData,
        \RosalynShop\RebuildUrl\Model\Product\UrlFactory $productUrlFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    ) {
        $this->_appState = $appState;
        $this->helperData = $helperData;
        $this->_productUrlFactory = $productUrlFactory;
        $this->storeRepository = $storeRepository;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('rosa:rebuild_url');
        $this->setDescription('Product Urls');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_appState->setAreaCode('adminhtml');
        try {
            $stores = $this->storeRepository->getList();
            $urls = $this->_productUrlFactory->create();
            $output->writeln('<info>Cleanup CatalogUrl Rewrites...</info>');
            $urls->cleanupCatalogUrlRewrites();
            foreach ($stores as $store) {
                $urls->startGenerateUrl($store->getId());
            }
            $output->writeln('<info>Reindex...</info>');
            $this->helperData->_runReindexing();
            $output->writeln('<info>Clean cache...</info>');
            $this->helperData->_runClearCache();
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        return \Magento\Framework\Console\Cli::RETURN_FAILURE;
    }
}
