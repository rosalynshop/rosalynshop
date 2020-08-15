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

use Mirasvit\ReportApi\Api\Config\FieldInterface;
use Mirasvit\ReportApi\Api\Config\SelectInterface;
use Mirasvit\ReportApi\Api\Config\TableInterface;

class Field implements FieldInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var TableInterface
     */
    protected $table;

    /**
     * @var bool
     */
    private $identity;

    public function __construct(
        TableInterface $table,
        $name,
        $identity = false
    ) {
        $this->table = $table;
        $this->name = $name;
        $this->identity = $identity;
    }

    public function getIdentifier()
    {
        return "{$this->table->getName()}|{$this->name}";
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->table;
    }

    public function setTable(TableInterface $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isIdentity()
    {
        return $this->identity;
    }

    /**
     * @return string
     */
    public function toDbExpr()
    {
        return $this->table->getName() . '.' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function join(SelectInterface $select)
    {
        return $select->joinTable($this->getTable());
    }

    public function __toString()
    {
        return "{$this->getIdentifier()}";
    }
}
