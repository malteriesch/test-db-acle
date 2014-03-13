<?php
namespace TestDbAcle\Commands;
class SetAutoIncrementCommand implements CommandInterface
{
    protected $pdoFacade;

    protected $table;
    protected $nextIncrement;
    
    public function __construct($table, $nextIncrement)
    {
        $this->table            = $table;
        $this->nextIncrement    = $nextIncrement;
        
    }
    public function execute()
    {
        $this->pdoFacade->setAutoIncrement($this->table, $this->nextIncrement);
    }

    public function initialise(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->pdoFacade    = $serviceLocator->get('pdoFacade');
    }
    
}
