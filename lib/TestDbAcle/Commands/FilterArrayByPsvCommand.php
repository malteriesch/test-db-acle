<?php
namespace TestDbAcle\Commands;


class FilterArrayByPsvCommand implements \TestDbAcle\Commands\CommandInterface
{
    protected $parser;
    protected $filterQueue;
    
    protected $placeHolders;
    protected $sourcePsv;
    
    protected $expectedData;
    protected $actualData;
    protected $actualDataUnfiltered;
    
    public function __construct($sourcePsv, $actualData, $placeHolders = array())
    {
        $this->placeHolders           = $placeHolders;
        $this->sourcePsv              = $sourcePsv;
        $this->actualDataUnfiltered   = $actualData;
        
    }
    
    public function execute()
    {
        $actualData           = array();
        
        $parsedTree = new \TestDbAcle\Psv\PsvTree();
        $parsedTree->addTable(new \TestDbAcle\Psv\Table\Table('default', $this->parser->parsePsv($this->sourcePsv)));
        
        $filteredParsedTree   = $this->filterQueue->filterDataTree($parsedTree);

        $expectedData = $filteredParsedTree->getTable(0)->toArray();
        
        if (count($expectedData) == 0){
            $this->expectedData = $expectedData;
            $this->actualData = $this->actualDataUnfiltered;
            return;
        } 
        
        $allowedColumns = array_keys($expectedData[0]);
        foreach ($this->actualDataUnfiltered as $row){
            $actualData[] = array_intersect_key($row, array_flip($allowedColumns));
        }
        $this->expectedData = $expectedData;
        $this->actualData = $actualData;
    }

    public function initialise(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->parser      = $serviceLocator->get('parser');
        
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
    
 
}
