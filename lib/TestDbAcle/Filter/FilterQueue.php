<?php

namespace TestDbAcle\Filter;

class FilterQueue
{

    protected $rowFilters = array();

    function addRowFilter(\TestDbAcle\Filter\RowFilter $filter)
    {
        $this->rowFilters[] = $filter;
    }

    function filterDataTree($dataTree)
    {
        foreach ($this->rowFilters as $filter) {
            $dataTree = $this->applyRowFilter($filter, $dataTree);
        }
        return $dataTree;
    }

    protected function filterTable(\TestDbAcle\Filter\RowFilter $filter, $table, array $tableData)
    {
        foreach ($tableData as $index => $tableRow) {
            $tableData[$index] = $filter->filter($table, $tableRow);
        }
        return $tableData;
    }

    protected function applyRowFilter($filter, $dataTree)
    {
        foreach ($dataTree->getTables() as $table) {
            $table->setData( $this->filterTable($filter, $table->getName(), $table->toArray()));
        }
        return $dataTree;
    }

}
