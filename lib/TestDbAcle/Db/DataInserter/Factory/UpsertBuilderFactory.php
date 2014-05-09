<?php

namespace TestDbAcle\Db\DataInserter\Factory;

class UpsertBuilderFactory
{
    function __construct(\TestDbAcle\Db\AbstractPdoFacade $pdoFacade)
    {
        $this->pdoFacade = $pdoFacade;
    }
    
    function getUpserter(\TestDbAcle\Psv\Table\Table $table, $valuesToBeInserted = array())
    {
        $tableName = $table->getName();
        if ($table->getMeta()->isReplaceMode()){
            $identifyMap = array();
        
            foreach ($table->getMeta()->getIdentifyColumns() as $idColumn) {
                $identifyMap[$idColumn]=$valuesToBeInserted[$idColumn];
            }

            if($this->pdoFacade->recordExists($tableName, $identifyMap)){
                return new \TestDbAcle\Db\DataInserter\Sql\UpdateBuilder($tableName,$identifyMap);
            } 
        }
        
        return new \TestDbAcle\Db\DataInserter\Sql\InsertBuilder($tableName);
    }
}