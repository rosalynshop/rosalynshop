<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Console\Command;

use Amasty\Fpc\Model\Config;
use Amasty\Fpc\Model\Queue;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Setup\Console\Command\AbstractSetupCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessQueue extends AbstractSetupCommand
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Queue $queue,
        State $state,
        Config $config,
        $name = null
    ) {
        parent::__construct($name);

        $this->queue = $queue;
        $this->state = $state;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('fpc:warmer:process')->setDescription('Starts the processing of the first batch of URLs.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_GLOBAL,
            [$this, 'process'],
            [$input, $output]
        );
    }

    public function process(InputInterface $input, OutputInterface $output)
    {
        try {
            $batchSize = $this->config->getBatchSize();
            $output->writeln("<info>Current batch size: $batchSize</info>");
            $output->writeln("<info>Starting warm up for first batch of URLs.</info>");
            $crawledPages = $this->queue->process();
            $output->writeln('');
            $output->writeln("<info>$crawledPages URLs has been successfully processed.</info>");

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Processing failed! Error: {$e->getMessage()}</error>");

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return Cli::RETURN_FAILURE;
        }
    }
}
