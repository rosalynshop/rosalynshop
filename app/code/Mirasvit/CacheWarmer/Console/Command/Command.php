<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.3
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Console\Command;

use Magento\Framework\ObjectManagerInterface;
use Mirasvit\CacheWarmer\Api\Repository\JobRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Service\JobServiceInterface;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    /**
     * @var JobServiceInterface
     */
    private $jobService;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    public function __construct(
        JobRepositoryInterface $jobRepository,
        JobServiceInterface $jobService,
        PageRepositoryInterface $pageRepository,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state
    ) {
        $this->jobRepository  = $jobRepository;
        $this->jobService     = $jobService;
        $this->pageRepository = $pageRepository;
        $this->objectManager  = $objectManager;
        $this->state  = $state;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:cache-warmer')
            ->setDescription('Various commands');

        $this->addOption('warm', null, null, 'Create and Run new warmer job');
        $this->addOption('remove-all-pages', null, null, 'Remove all pages');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        if ($input->getOption('warm')) {
            $job = $this->jobRepository->create();
            $this->jobRepository->save($job);

            $output->writeln("Job #{$job->getId()} was scheduled");
            $this->jobService->run($job);
            $output->writeln("Job was finished with status `{$job->getStatus()}`");
        } elseif ($input->getOption('remove-all-pages')) {
            $collection = $this->pageRepository->getCollection();
            foreach ($collection as $page) {
                $this->pageRepository->delete($page);
            }

            $output->writeln('done');
        } else {
            $help = new HelpCommand();
            $help->setCommand($this);

            return $help->run($input, $output);
        }
    }
}
