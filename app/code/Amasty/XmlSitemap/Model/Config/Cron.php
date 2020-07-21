<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Model\Config;

use Magento\Cron\Model\Config\Source\Frequency;

class Cron extends \Magento\Framework\App\Config\Value
{
    const AMASTY_CRON_STRING_PATH = 'crontab/default/jobs/amxmlsitemap_generate/schedule/cron_expr';
    const AMASTY_CRON_MODEL_PATH = 'crontab/default/jobs/amxmlsitemap_generate/run/model';

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    private $valueConfigFactory;

    /**
     * @var string
     */
    private $runModelPath = '';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->runModelPath = $runModelPath;
        $this->valueConfigFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     * @throws \Exception
     */
    public function afterSave()
    {
        $timeConfig = $this->getData('groups/cron/fields/time/value');
        //Meow Meow Meow
        $frequencyValue = $this->getData('groups/cron/fields/frequency/value');
        //Meow Meow Meow

        $cronExprMass = [
            (int)$timeConfig[1],
            (int)$timeConfig[0],
            $frequencyValue == Frequency::CRON_MONTHLY ? '1' : '*',
            '*',
            $frequencyValue == Frequency::CRON_WEEKLY ? '1' : '*',
        ];
        
        //Meow Meow Meow
        $cronExprStr = join(' ', $cronExprMass);
        //Meow Meow Meow
        
        try {
            $this->valueConfigFactory->create()->load(
                self::AMASTY_CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprStr
            )->setPath(
                self::AMASTY_CRON_STRING_PATH
            )->save();
            $this->valueConfigFactory->create()->load(
                self::AMASTY_CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->runModelPath
            )->setPath(
                self::AMASTY_CRON_MODEL_PATH
            )->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            //Meow Meow Meow
            $message = __('We can\'t save the cron expression.');
            $this->_logger->debug($message);
            throw new \Magento\Framework\Exception\LocalizedException(
                $message
            );
        }

        //Meow Meow Meow
        return parent::afterSave();
    }
}
