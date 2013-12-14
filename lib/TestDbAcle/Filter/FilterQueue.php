<?php

namespace TestDbAcle\Filter;

class FilterQueue
{

    protected $rowFilters = array();

    function addRowFilter(\TestDbAcle\Filter\RowFilter $filter)
    {
        $this->rowFilters[] = $filter;
    }

    function filterDataTree(array $dataTree)
    {
        foreach ($this->rowFilters as $filter) {
            foreach ($dataTree as $table => $tableData) {
                $dataTree[$table]['data'] = $this->filterTable($filter, $table, $tableData['data']);
            }
        }
        return $dataTree;
    }

    function filterTable(\TestDbAcle\Filter\RowFilter $filter, $table, array $tableData)
    {
        foreach ($tableData as $index => $tableRow) {
            $tableData[$index] = $filter->filter($table, $tableRow);
        }
        return $tableData;
    }

}
