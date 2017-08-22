<?php

namespace TestDbAcle\Psv;

/**
 * @TODO this is currently covered implicitly by PsvParseTest
 */
class PsvTree 
{
    protected $tables;
    function __construct(array $tables = array())
    {
        $this->tables = $tables;
    }
    
    function addTable(\TestDbAcle\Psv\Table\Table $table)
    {
        return $this->tables[] = $table;
    }
    
    
    function getTable($index)
    {
        return $this->tables[$index];
    }
    
    function getTables()
    {
        return $this->tables;
    }

    function toArray()
    {
        $out = array();
        foreach ($this->getTableIndexes() as $table) {
            $out[$this->getTable($table)->getName()] = $this->getTable($table)->toArray();
        }
        return $out;
    }

    function getTableIndexes()
    {
        return array_keys($this->tables);
    }

    function getTableNames()
    {
        $out = array();
        foreach ($this->getTableIndexes() as $table) {
            $out[] = $this->getTable($table)->getName();
        }
        return $out;
    }
}