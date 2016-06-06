<?php
namespace TestDbAcle\Commands;

use TestDbAcle\Db\AbstractTableFactory;
use TestDbAcle\Db\DataInserter\DataInserter;
use TestDbAcle\Db\TableList;
use TestDbAcle\Filter\FilterQueue;
use TestDbAcle\Psv\PsvParser;

/**
 * @TODO this is currently covered only by the functional tests. 
 */
class SetupTablesCommand implements CommandInterface
{
    /**
     * @var PsvParser
     */
    protected $parser;

    /**
     * @var DataInserter
     */
    protected $dataInserter;

    /**
     * @var FilterQueue
     */
    protected $filterQueue;

    /**
     * @var TableList
     */
    protected $tableList;

    /**
     * @var AbstractTableFactory
     */
    protected $tableFactory;

    protected $placeHolders;
    protected $sourcePsv;
    
    public function __construct($sourcePsv, $placeHolders = array())
    {
        $this->placeHolders = $placeHolders;
        $this->sourcePsv    = $sourcePsv;
        
    }
    
    public function execute()
    {
        $parsedTree = $this->parser->parsePsvTree($this->sourcePsv);
        $this->tableFactory->populateTableList(array_keys($parsedTree->getTables()),$this->tableList);
        $this->dataInserter->process($this->filterQueue->filterDataTree($parsedTree));
    }

    public function initialise(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->parser       = $serviceLocator->get('parser');
        $this->filterQueue  = $serviceLocator->createNew('dataInserterFilterQueue');
        $this->dataInserter = $serviceLocator->get('dataInserter');
        $this->tableList    = $serviceLocator->get('tableList');
        $this->tableFactory = $serviceLocator->get('tableFactory');
        if($this->placeHolders){
            $this->filterQueue->addRowFilter( new \TestDbAcle\Filter\PlaceholderRowFilter($this->placeHolders));
        }
    }
    
}
