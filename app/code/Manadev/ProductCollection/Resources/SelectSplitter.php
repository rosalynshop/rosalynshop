<?php

namespace Manadev\ProductCollection\Resources;

use Magento\Framework\DB\Select;
use Manadev\Core\Exceptions\NotImplemented;
use Manadev\Core\Exceptions\NotSupported;
use Magento\Framework\Model\ResourceModel\Db;
use Manadev\Core\Resources\TemporaryResource;

class SelectSplitter extends Db\AbstractDb
{
    const MAX_FROM = 5;

    /**
     * @var Select
     */
    protected $select;

    /**
     * @var Select[]
     */
    protected $stack;
    /**
     * @var TemporaryResource
     */
    protected $temporaryResource;

    public function __construct(Db\Context $context,
        TemporaryResource $temporaryResource, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->temporaryResource = $temporaryResource;
    }

    protected function _construct() {
        $this->_setMainTable('catalog_product_entity');
    }

    public function begin(Select $select) {
        if ($this->select) {
            throw new NotSupported('Recursive use of SelectSplitter not supported');
        }

        $this->select = $select;
        $this->stack = [$select];
    }

    public function end() {
        if (!$this->select) {
            throw new NotSupported('Call SelectSplitter::begin() before calling end()');
        }

        $this->select = null;
        $this->stack = null;
    }

    public function with(Select $select, callable $callback) {
        $this->begin($select);

        try {
            return $callback();
        }
        finally {
            $this->end();
        }
    }

    public function split(Select $select) {
        if ($this->select !== $select) {
            throw new NotSupported('Now splitting some other SELECT');
        }

        $select = $this->stack[count($this->stack) - 1];

        if (count($select->getPart('from')) < static::MAX_FROM) {
            return $select;
        }

        return $this->push();
    }

    protected function push() {
        $select = $this->getConnection()->select()
            ->from(['e' => '{{e}}']);

        $this->stack[] = $select;

        return $select;
    }

    public function run() {
        throw new NotImplemented();
    }

    public function join() {
        $table = null;

        for ($i = 1; $i < count($this->stack); $i++) {
            $select = $this->stack[$i];
            $table = $this->runSplitSelect($select, $table ?? $this->getMainTable());
        }

        if ($table) {
            $this->select->joinInner(['mana_split' => $table],
                "`e`.`entity_id` = `mana_split`.`entity_id`", null);
        }
    }

    protected function runSplitSelect(Select $select, $table) {
        // replace source table in SELECT
        $from = $select->getPart('from');
        $from['e']['tableName'] = $table;
        $select->setPart('from', $from);

        // create temp table
        $result = $this->temporaryResource->createProductIdTable();

        $select->reset(Select::ORDER);
        $select->reset(Select::LIMIT_COUNT);
        $select->reset(Select::LIMIT_OFFSET);
        $select->reset(Select::COLUMNS);
        $select->resetJoinLeft();
        $select->distinct();
        $select->columns('e.entity_id');

        $this->getConnection()->query($select->insertIgnoreFromSelect($result));

        return $result;
    }

}
