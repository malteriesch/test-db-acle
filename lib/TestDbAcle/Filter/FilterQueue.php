<?php

namespace TestDbAcle\Filter;

use TestDbAcle\Psv\Table\Table;

class FilterQueue
{

    protected $rowFilters = array();

    function addRowFilter(\TestDbAcle\Filter\RowFilter $filter)
    {
        $this->rowFilters[] = $filter;
    }

    function filterDataTree(\TestDbAcle\Psv\PsvTree $dataTree)
    {
        foreach ($this->rowFilters as $filter) {
            $dataTree = $this->applyRowFilter($filter, $dataTree);
        }
        return $dataTree;
    }

    protected function filterTable(\TestDbAcle\Filter\RowFilter $filter, Table $table)
    {
        $tableName = $table->getName();
        $tableData = $table->toArray();
        foreach ($tableData as $index => $tableRow) {
            if (!$filter->skip($table)) {
                $tableData[$index] = $filter->filter($tableName, $tableRow);
            }
        }
        return $tableData;
    }

    protected function applyRowFilter($filter, \TestDbAcle\Psv\PsvTree $dataTree)
    {
        foreach ($dataTree->getTables() as $table) {
            $table->setData( $this->filterTable($filter, $table));
        }
        return $dataTree;
    }

}
