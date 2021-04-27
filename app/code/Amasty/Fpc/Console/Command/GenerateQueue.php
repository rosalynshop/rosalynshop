<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Console\Command;

use Amasty\Fpc\Model\Queue;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Setup\Console\Command\AbstractSetupCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateQueue extends AbstractSetupCommand
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var State
     */
    private $state;

    public function __construct(
        Queue $queue,
        State $state,
        $name = null
    ) {
        parent::__construct($name);

        $this->queue = $queue;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('fpc:warmer:generate')->setDescription('Generates the queue for warming');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_GLOBAL,
            [$this, 'generate'],
            [$input, $output]
        );
    }

    public function generate(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('<info>Starting generation of warmer queue...</info>');
            list($result, $items) = $this->queue->generate();
            $output->writeln('');
            $output->writeln("<info>Warmer queue has been successfully generated for $items URLs.</info>");

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Generation failed! Error: {$e->getMessage()}</error>");

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return Cli::RETURN_FAILURE;
        }
    }
}
