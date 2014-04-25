<?php
namespace TestDbAcle\Commands;
class SetupTablesCommand implements CommandInterface
{
    protected $parser;
    protected $dataInserter;
    protected $filterQueue;
    protected $tableInfo;
    protected $pdoFacade;

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
        foreach($parsedTree->getTables() as $table){
            $tableName = $table->getName();
            $this->tableInfo->addTable(new \TestDbAcle\Db\Table\Table($tableName, $this->pdoFacade->describeTable($tableName) ) );
        }
        return $this->dataInserter->process($this->filterQueue->filterDataTree($parsedTree));
    }

    public function initialise(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->parser       = $serviceLocator->get('parser');
        $this->filterQueue  = $serviceLocator->createNew('dataInserterFilterQueue');
        $this->dataInserter = $serviceLocator->get('dataInserter');
        $this->tableInfo    = $serviceLocator->get('tableInfo');
        $this->pdoFacade    = $serviceLocator->get('pdoFacade');
        if($this->placeHolders){
            $this->filterQueue->addRowFilter( new \TestDbAcle\Filter\PlaceholderRowFilter($this->placeHolders));
        }
    }
    
}
