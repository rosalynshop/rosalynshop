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

use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\CacheWarmer\Api\Data\UserAgentInterface;
use Mirasvit\CacheWarmer\Service\CurlService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlCommand extends Command
{
    const CYCLE_LIMIT = 100;

    /**
     * Use in preg_match
     * @var array
     */
    private $ignorePatternPool
        = [
            '\\/customer',
            '\\/checkout',
            '\\/catalogsearch',
            '\\/wishlist',
            '\\/sendfriend',
            '\\/downloadable',
        ];

    /**
     * @var array
     */
    private $pool = [];

    /**
     * @var array
     */
    private $hostPool = [];

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CurlService
     */
    private $curlService;

    public function __construct(
        CurlService $curlService,
        State $appState,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->curlService   = $curlService;
        $this->appState      = $appState;
        $this->objectManager = $objectManager;
        $this->storeManager  = $storeManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:cache-warmer:crawl')
            ->setDescription('Crawl all pages');

        $this->addOption('base-url', null, InputOption::VALUE_REQUIRED, 'Set base url');
        $this->addOption('store-id', null, InputOption::VALUE_REQUIRED, 'Set store id');

        $this->addOption('ignore-query', null, InputOption::VALUE_NONE, 'Ignore links with query params (?)');
        $this->addOption('ignore-http', null, InputOption::VALUE_NONE, 'Ignore links with http');
        $this->addOption('ignore-https', null, InputOption::VALUE_NONE, 'Ignore links with https');

        $this->addOption(
            'cycle-limit',
            null,
            InputOption::VALUE_REQUIRED,
            'The number of cycles (default value ' . self::CYCLE_LIMIT . ')'
        );

        $this->addOption('unlock', null, InputOption::VALUE_NONE, 'Unlock');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('unlock')) {
            $this->unlock();
        }

        if ($this->isLocked()) {
            $output->writeln('<comment>Current process is running or was finished incorrectly.</comment>');
            $output->writeln('To unlock run with option "--unlock"');

            return false;
        }

        $this->lock();

        try {
            $this->appState->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        $cycleLimit = $input->getOption('cycle-limit') ? : self::CYCLE_LIMIT;
        $output->writeln("Current 'cycle_limit' is $cycleLimit");

        $this->initializePool($input);

        $idx   = 0;
        $cycle = 0;

        while (true) {
            $cycle++;

            if (count($this->pool) == $idx) {
                $output->writeln('<info>All URLs were crawled!</info>');
                break;
            }

            if ($cycle > $cycleLimit) {
                $output->writeln('<comment>Done</comment>');
                break;
            } else {
                $output->writeln("<comment>Cycle $cycle</comment>");
            }

            foreach ($this->pool as $url => $level) {
                if ($level === 0) {
                    continue;
                }

                $idx++;

                $memoryUsage = round(memory_get_usage() / 1048576, 2);

                $output->writeln(sprintf(
                    '%s/%s %s %s (memory usage: %s Mb)',
                    $idx,
                    count($this->pool),
                    $level,
                    $url,
                    $memoryUsage
                ));

                $urls = $this->getUrls($url, $output);
                foreach ($urls as $newUrl) {
                    $this->addUrlToPool($input, $newUrl, $level + 1);
                }

                $this->pool[$url] = 0;
            }
        }

        $this->unlock();
    }

    /**
     * @return void
     */
    private function unlock()
    {
        $lockFile = $this->getLockFile();
        if (is_file($lockFile)) {
            unlink($lockFile);
        }
    }

    /**
     * @return string
     */
    private function getLockFile()
    {
        $tmpPath  = $this->objectManager
            ->get(\Mirasvit\CacheWarmer\Model\Config::class)
            ->getTmpPath();
        $lockFile = $tmpPath . '/cache-warmer.cli.crawl.lock';

        return $lockFile;
    }

    /**
     * @return bool
     */
    private function isLocked()
    {
        $lockFile = $this->getLockFile();
        if (file_exists($lockFile)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function lock()
    {
        $lockFile = $this->getLockFile();

        $lockPointer = fopen($lockFile, "w");
        fwrite($lockPointer, date('c'));
        fclose($lockPointer);

        return true;
    }

    /**
     * @param InputInterface $input
     * @return $this
     */
    private function initializePool(InputInterface $input)
    {
        $storeId = $input->getOption('store-id');
        $baseUrl = $input->getOption('base-url');

        $baseUrls = [];
        if ($storeId) {
            /** @var \Magento\Store\Model\Store $store */
            $store      = $this->storeManager->getStore($storeId);
            $baseUrls[] = $store->getBaseUrl();
        } elseif ($baseUrl) {
            $baseUrls[] = $baseUrl;
        } else {
            /** @var \Magento\Store\Model\Store $store */
            foreach ($this->storeManager->getStores() as $store) {
                $baseUrls[] = $store->getBaseUrl();
            }
        }

        foreach ($baseUrls as $url) {
            $this->hostPool[] = parse_url($url, PHP_URL_HOST);
            $this->addUrlToPool($input, $url, 1);
        }

        return $this;
    }

    /**
     * @param InputInterface $input
     * @param string         $url
     * @param int            $level
     * @return bool
     */
    private function addUrlToPool(InputInterface $input, $url, $level)
    {
        if (isset($this->pool[$url])) {
            return false;
        }

        if (in_array(pathinfo($url, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'pdf', 'zip', 'rar'])) {
            return false;
        }

        $schema = parse_url($url);

        if (!isset($schema['host']) || isset($schema['fragment'])) {
            return false;
        }

        if ($input->getOption('ignore-query') && isset($schema['query'])) {
            return false;
        }

        if ($input->getOption('ignore-http') && $schema['scheme'] === 'http') {
            return false;
        }

        if ($input->getOption('ignore-https') && $schema['scheme'] === 'https') {
            return false;
        }

        if (!in_array($schema['host'], $this->hostPool)) {
            return false;
        }

        $pattern = implode('|', $this->ignorePatternPool);
        if (preg_match('/' . $pattern . '/ims', $url)) {
            return false;
        }

        $this->pool[$url] = $level;

        return true;
    }

    /**
     * @param string $url
     * @param OutputInterface $output
     * @return array
     */
    private function getUrls($url, OutputInterface $output)
    {
        $channel = $this->curlService->initChannel();
        $channel->setUrl($url);
        $channel->setOption(CURLOPT_FOLLOWLOCATION, true);

        $userAgent = UserAgentInterface::DESKTOP_USER_AGENT;

        $channel->setUserAgent($userAgent);

        $response = $this->curlService->request($channel);
        if ($response->getCode() == 401) {
            $output->writeln("<error>Can't open URL. 401 Authorization Required. Set HTTP Access info in the extension settings</error>");
            return [];
        }

        $content = $response->getBody();

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML($content);
        libxml_clear_errors();

        $links = $dom->getElementsByTagName('a');

        $result = [];
        /** @var \DOMElement $link */
        foreach ($links as $link) {
            $result[] = $link->getAttribute('href');
        }

        return $result;
    }
}
