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
 * @package   mirasvit/module-report-api
 * @version   1.0.12
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Config\Entity;

use Magento\Framework\App\CacheInterface;
use Mirasvit\ReportApi\Config\Loader\Map;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Eav\Model\Config;
use Mirasvit\ReportApi\Service\TableService;

class EavTable extends Table
{
    /**
     * @var EavEntityFactory
     */
    private $eavEntityFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var EavFieldFactory
     */
    private $eavFieldFactory;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Map
     */
    private $map;

    public function __construct(
        Map $map,
        EavFieldFactory $eavFieldFactory,
        EavEntityFactory $eavEntityFactory,
        CacheInterface $cache,
        Config $eavConfig,
        $type,
        TableService $tableService,
        FieldFactory $fieldFactory,
        $name,
        $label,
        $group = null,
        $connection = 'default'
    ) {
        parent::__construct($tableService, $fieldFactory, $name, $label, $group, $connection);

        $this->map = $map;
        $this->eavFieldFactory = $eavFieldFactory;
        $this->eavEntityFactory = $eavEntityFactory;
        $this->eavConfig = $eavConfig;
        $this->cache = $cache;

        $this->initByEntityType($type);
    }

    /**
     * @param string $entityType
     * @return void
     */
    protected function initByEntityType($entityType)
    {
        $entityTypeId = (int)$this->eavEntityFactory->create()->setType($entityType)->getTypeId();

        $attributeCodes = $this->eavConfig->getEntityAttributeCodes($entityTypeId);
        foreach ($attributeCodes as $attributeCode) {
            if (in_array($attributeCode, ['category_ids', 'media_gallery'])) {
                continue;
            }

            $attribute = $this->eavConfig->getAttribute($entityTypeId, $attributeCode);

            $field = $this->eavFieldFactory->create([
                'table'        => $this,
                'name'         => $attributeCode,
                'entityTypeId' => $entityType,
            ]);

            $this->fieldsPool[$field->getName()] = $field;

            if ($attribute->getDefaultFrontendLabel()) {
                $options = false;

                if ($attribute->usesSource()) {
                    $identifier = $attribute->getAttributeCode() . 'options';
                    $cache = $this->cache->load($identifier);
                    if ($cache) {
                        $options = \Zend_Json::decode($cache);
                    } elseif ($attribute->getSourceModel() && !class_exists($attribute->getSourceModel())) {
                        unset($this->fieldsPool[$field->getName()]);
                        continue;
                    } else {
                        $options = $attribute->getSource()->toOptionArray();
                        $this->cache->save(\Zend_Json::encode($options), $identifier);
                    }
                }

                $this->map->initColumn([
                    'name'    => $attributeCode,
                    'table'   => $this,
                    'type'    => $this->resolveType($attribute->getFrontendInput()),
                    'options' => $options,
                    'label'   => $attribute->getDefaultFrontendLabel(),
                ]);
            }
        }
    }

    public function resolveType($typeName)
    {
        switch ($typeName) {
            case 'text':
            case 'boolean':
            case 'hidden':
            case 'multiline':
            case 'textarea':
            case 'gallery':
            case 'media_image':
                $typeName = 'string';
                break;
            case 'select':
            case 'multiselect':
                $typeName = 'select';
                break;
            case 'price':
                $typeName = 'money';
                break;
            default:
                $typeName = 'string';
        }

        return $typeName;
    }
}
