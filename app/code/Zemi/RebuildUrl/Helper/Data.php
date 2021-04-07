<?php
/*
 * @author   Zemi <cskh.zemifashion@gmail.com>
 * @copyright Copyright (c) 2021 Zemi <cskh.zemifashion@gmail.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Zemi\RebuildUrl\Helper;

use Symfony\Component\Console\Helper\ProgressBar;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConn;

    /**
     * @var \Magento\Customer\Model\Session\Proxy
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList\Proxy
     */
    private $dir;

    private $_attributeId;

    private $_productEntityTypeId = 0;

    protected $input;
    protected $output;

    protected $progressbar;
    protected $progressFormat = '%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resourceConn
     * @param \Magento\Customer\Model\Session\Proxy $customerSession
     * @param \Magento\Framework\Filesystem\DirectoryList\Proxy $dir
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConn,
        \Magento\Customer\Model\Session\Proxy $customerSession,
        \Magento\Framework\Filesystem\DirectoryList\Proxy $dir,
        \Symfony\Component\Console\Output\ConsoleOutput $output
    )
    {
        parent::__construct($context);
        $this->resourceConn = $resourceConn;
        $this->customerSession = $customerSession;
        $this->dir = $dir;
        $this->output = $output;
    }

    /**
     * @param $configPath
     * @return mixed
     */
    public function getStoreConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * @param $query
     * @return \Zend_Db_Statement_Interface
     */
    public function _doQuery($query)
    {
        return $this->getConnection()->query($query);
    }

    /**
     * @param $tableName
     * @return string
     */
    public function _getTableName($tableName)
    {
        return $this->getConnection()->getTableName($tableName);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
        return $this->resourceConn->getConnection();
    }

    /**
     * @param $attributeCode
     * @return mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function _getCategoryAttributeId($attributeCode)
    {
        return $this->_getAttributeId($attributeCode, 'catalog_category');
    }

    /**
     * @param $attributeCode
     * @param $typeCode
     * @return mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function _getAttributeId($attributeCode, $typeCode)
    {
        if ($typeCode == 'catalog_product') {
            $typeId = $this->_getProductEntityTypeId();
        } else {
            $typeId = $this->_getEntityTypeId($typeCode);
        }

        if (!isset($this->_attributeId[$typeCode]) or !is_array(
                $this->_attributeId[$typeCode]
            )
        ) {
            $sql = "
                    SELECT attribute_id, attribute_code
                    FROM " . $this->_getTableName('eav_attribute') . "
                    WHERE entity_type_id = '" . $typeId . "'
                   ";

            $result = $this->_doQuery($sql)->fetchAll();

            if ($result) {
                foreach ($result as $resultItem) {
                    $this->_attributeId[$typeCode][$resultItem['attribute_code']]
                        = $resultItem['attribute_id'];
                }
            }
        }

        return $this->_attributeId[$typeCode][$attributeCode];
    }

    /**
     * @return bool|mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function _getProductEntityTypeId()
    {
        if (!$this->_productEntityTypeId) {
            $this->_productEntityTypeId = $this->_getEntityTypeId(
                'catalog_product'
            );
        }

        return $this->_productEntityTypeId;
    }

    /**
     * @param $code
     * @return bool|mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function _getEntityTypeId($code)
    {
        $sql = "
            SELECT entity_type_id
            FROM " . $this->_getTableName('eav_entity_type') . "
            WHERE entity_type_code = '" . $code . "'
            LIMIT 1
        ";
        $result = $this->_doQuery($sql)->fetch();

        if ($result) {
            return $result['entity_type_id'];
        }

        return false;
    }

    /**
     * @param $attributeCode
     * @return mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function _getProductAttributeId($attributeCode)
    {
        return $this->_getAttributeId($attributeCode, 'catalog_product');
    }

    /**
     * process
     */
    public function advanceProgressBar()
    {
        $this->progressbar->advance();
    }

    /**
     * @param $message
     */
    public function finishProgressBar($message)
    {
        $this->progressbar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>' . $message . '</info>');
    }

    /**
     * @param $message
     * @param $total
     */
    public function startProgressBar($message, $total)
    {
        $this->progressbar = new ProgressBar($this->output, $total);
        $this->progressbar->setFormat($this->progressFormat);
        $this->output->writeln('<info>' . $message . '</info>');
        $this->progressbar->start();
        $this->progressbar->display();
    }

    /**
     * Run reindexation
     *
     * @return void
     */
    public function _runReindexing()
    {
        shell_exec('php bin/magento indexer:reindex');
    }

    /**
     * Clear cache
     *
     * @return void
     */
    public function _runClearCache()
    {
        shell_exec('php bin/magento cache:clean');
        shell_exec('php bin/magento cache:flush');
    }

    /**
     * @param $str
     * @return string|string[]|null
     */
    public function convertTextVN($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace("/(\“|\”|\‘|\’|\,|\!|\&|\;|\@|\#|\%|\~|\`|\=|\_|\'|\]|\[|\}|\{|\)|\(|\+|\^)/", '-', $str);
        $str = preg_replace("/( )/", '-', $str);
        return $str;
    }
}
