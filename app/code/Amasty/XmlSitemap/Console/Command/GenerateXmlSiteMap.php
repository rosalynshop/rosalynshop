<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Console\Command;

use Amasty\XmlSitemap\Model\ResourceModel\Sitemap\CollectionFactory;
use Magento\Setup\Console\Command\AbstractSetupCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateXmlSiteMap extends AbstractSetupCommand
{
    /**
     * @var CollectionFactory
     */
    private $sitemapCollection;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    public function __construct(
        CollectionFactory $sitemapCollection,
        \Magento\Framework\App\State $state,
        $name = null
    ) {
        $this->sitemapCollection = $sitemapCollection;
        $this->state = $state;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('amasty:xmlsitemap:generate')
            ->setDescription('Generates Amasty Xml Sitemap');

        $this->setDefinition([
            new InputArgument(
                'id',
                InputArgument::OPTIONAL,
                'Sitemap Id. Default: All'
            )
        ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->emulateAreaCode(
            'frontend',
            [$this, 'generate'],
            [$input, $output]
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function generate(InputInterface $input, OutputInterface $output)
    {
        try {
            $siteMapId = (int)$input->getArgument('id');

            /** @var \Amasty\XmlSitemap\Model\ResourceModel\Sitemap\Collection $profiles */
            $profiles = $this->sitemapCollection->create();
            if ($siteMapId) {
                $profiles->addFieldToFilter('id', $siteMapId);
            }

            if ($profiles->getSize()) {
                /** @var \Amasty\XmlSitemap\Model\Sitemap $profile */
                foreach ($profiles as $profile) {
                    $profile->generateXml();
                }

                $output->writeln('<info>Sitemap has been generated successfully</info>');
            } else {
                $output->writeln('<error>We can\'t find Sitemap</error>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
