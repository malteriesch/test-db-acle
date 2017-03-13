<?php

namespace TestDbAcle\Db\DataInserter\Factory;

class UpsertBuilderFactory
{
    function __construct(\TestDbAcle\Db\AbstractPdoFacade $pdoFacade)
    {
        $this->pdoFacade = $pdoFacade;
    }
    
    function getUpserter(\TestDbAcle\Psv\Table\Table $psvTable, \TestDbAcle\Db\Table\Table $dbTable, $valuesToBeInserted = array())
    {
        $tableName = $psvTable->getName();
        if ($psvTable->getMeta()->isReplaceMode()){
            $identifyMap = array();

            if ($psvTable->getMeta()->getIdentifyColumns()) {
                foreach ($psvTable->getMeta()->getIdentifyColumns() as $idColumn) {
                    $identifyMap[$idColumn]=$valuesToBeInserted[$idColumn];
                }
            } else {
                $identifyMap[$dbTable->getPrimaryKey()]=$valuesToBeInserted[$dbTable->getPrimaryKey()];
            }



            if($this->pdoFacade->recordExists($tableName, $identifyMap)){
                return new \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder($tableName,$identifyMap);
            } 
        }
        
        return new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder($tableName);
    }
}