<?php
namespace TestDbAcle\Commands;

/**
 * @TODO this is currently covered only by the functional tests. 
 */
class FilterTableStateByPsvCommand implements CommandInterface
{
    protected $parser;
    protected $filterQueue;
    protected $tableList;
    protected $pdoFacade;
    protected $tableFactory;
    
    protected $placeHolders;
    protected $sourcePsv;
    
    protected $expectedData;
    protected $sourceData;
    
    public function __construct($sourcePsv, $placeHolders = array())
    {
        $this->placeHolders = $placeHolders;
        $this->sourcePsv    = $sourcePsv;
        
    }
    
    public function execute()
    {
        $expectedData         = array();
        $actualData           = array();
        $parsedTree           = $this->parser->parsePsvTree($this->sourcePsv);
        
        $filteredParsedTree   = $this->filterQueue->filterDataTree($parsedTree);

        $this->tableFactory->populateTableList(array_keys($filteredParsedTree->getTables()),$this->tableList);

        foreach($filteredParsedTree->getTables() as $table){
            $tableName = $table->getName();
            $expectedData[$tableName]=$table->toArray();
            $columnsToSelect = isset($expectedData[$tableName][0]) ? implode(", ",array_keys($expectedData[$tableName][0])) : '*';
            $data = $this->pdoFacade->getQuery("select $columnsToSelect from $tableName");
            $actualData[$tableName] = $this->truncateDatetimeFields($table, $data);
        }
        $this->actualData = $actualData;
        $this->expectedData = $expectedData;
    }

    public function initialise(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->parser      = $serviceLocator->get('parser');
        //$this->filterQueue = $serviceLocator->get('filterQueue');
        $this->tableList   = $serviceLocator->get('tableList');
        $this->pdoFacade   = $serviceLocator->get('pdoFacade');
        $this->tableFactory = $serviceLocator->get('tableFactory');
        
        $this->filterQueue          = new \TestDbAcle\Filter\FilterQueue();
        $this->filterQueue->addRowFilter(new \TestDbAcle\Filter\PlaceholderRowFilter($this->placeHolders));
        
    }
    
    /**
     * @return array An associative array representing the PSV
     */
    function getExpectedData()
    {
        return $this->expectedData;
    }
    
    /**
     * @return array An associative array representing the table state as configured by the Psv
     */
    function getActualData()
    {
        return $this->actualData;
    }
    
    protected function truncateDatetimeFields(\TestDbAcle\Psv\Table\Table $table, array $tableDataFromDb)
    {
        $tableName = $table->getName();
        $truncateableColumns = $table->getMeta()->getTruncateDateColumns();
        $filtered = array();
        foreach($tableDataFromDb as $dataRow){
            $newFilteredRow = array();
            foreach($dataRow as $columnName=>$value){
                $isDatetime = $this->tableList->getTable($tableName)->getColumn($columnName)->isDateTime() || in_array($columnName, $truncateableColumns);
                if ($isDatetime && $value){
                    $newFilteredRow[$columnName] = date("Y-m-d", strtotime($value));
                }else{
                    $newFilteredRow[$columnName] = $value;
                }
            }
            $filtered[]=$newFilteredRow;
        }
        return $filtered;
    }
}
