<?php
namespace TestDbAcle\Commands;

use TestDbAcle\Db\AbstractPdoFacade;
use TestDbAcle\Db\AbstractTableFactory;
use TestDbAcle\Db\Mysql\Pdo\PdoFacade;
use TestDbAcle\Db\TableList;
use TestDbAcle\Filter\FilterQueue;
use TestDbAcle\Psv\PsvParser;
use TestDbAcle\Psv\Table\Table;

/**
 * @TODO this is currently covered only by the functional tests. 
 */
class FilterTableStateByPsvCommand implements CommandInterface
{
    /**
     * @var PsvParser $parser
     */
    protected $parser;

    /**
     * @var FilterQueue $filterQueue
     */
    protected $filterQueue;

    /**
     * @var TableList
     */
    protected $tableList;

    /** @var  AbstractPdoFacade */
    protected $pdoFacade;

    /**
     * @var AbstractTableFactory $tableFactory
     */
    protected $tableFactory;

    /**
     * @var array $placeHolders
     */
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
            /** @var Table $table */
            $tableName = $table->getName();

            $expectedData[$tableName]=$table->toArray();
            
            if(isset($expectedData[$tableName][0])) {
                $columns = array_keys($expectedData[$tableName][0]);
                array_walk($columns, function(&$value){
                    $value ="`$value`";
                });
                $columnsToSelect = implode(", ", $columns);
                $data = $this->pdoFacade->getQuery("select $columnsToSelect from `$tableName`");
            } else {
                $data = $this->pdoFacade->getQuery("select * from `$tableName`");
            }
            
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
