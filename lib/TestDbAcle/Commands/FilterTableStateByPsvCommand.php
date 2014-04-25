<?php
namespace TestDbAcle\Commands;
class FilterTableStateByPsvCommand implements CommandInterface
{
    protected $parser;
    protected $filterQueue;
    protected $tableInfo;
    protected $pdoFacade;
    
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

        foreach($filteredParsedTree->getTables() as $table){
            $this->tableInfo->addTable(new \TestDbAcle\Db\Table\Table($table->getName(), $this->pdoFacade->describeTable($table->getName())));
        }

        foreach($filteredParsedTree->getTables() as $table){
            $tableName = $table->getName();
            $expectedData[$tableName]=$table->toArray();
            $columnsToSelect = array_keys($expectedData[$tableName][0]);
            $data = $this->pdoFacade->getQuery("select ".implode(", ",$columnsToSelect)." from $tableName");
            $actualData[$tableName] = $this->truncateDatetimeFields($tableName, $data);
        }
        $this->actualData = $actualData;
        $this->expectedData = $expectedData;
    }

    public function initialise(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->parser      = $serviceLocator->get('parser');
        //$this->filterQueue = $serviceLocator->get('filterQueue');
        $this->tableInfo   = $serviceLocator->get('tableInfo');
        $this->pdoFacade   = $serviceLocator->get('pdoFacade');
        
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
    
    protected function truncateDatetimeFields($tableName, $tableData)
    {
        $filtered = array();
        foreach($tableData as $dataRow){
            $newFilteredRow = array();
            foreach($dataRow as $columnName=>$value){
                if ($this->tableInfo->getTable($tableName)->getColumn($columnName)->isDateTime() && $value){
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
