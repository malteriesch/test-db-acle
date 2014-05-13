<?php

namespace TestDbAcle;

class TestDbAcle
{
    
    protected $parser;
    protected $filterQueue;
    protected $dataInserter;
    protected $tableList;
    protected $pdoFacade;
    protected $serviceLocator;
    
    public function runCommand(\TestDbAcle\Commands\CommandInterface $command)
    {
        $command->initialise($this->serviceLocator);
        $command->execute();
    }
  
    public function setServiceLocator(\TestDbAcle\ServiceLocator $serviceLocator)
    {
        $this->serviceLocator=$serviceLocator;
    }
    
    /**
     * @return \TestDbAcle\ServiceLocator
     */
    public function getServiceLocator()
    {
        return  $this->serviceLocator;
    }
       
    /**
     * @return TestDbAcle;
     */
    public static function create(\Pdo $pdo, $factoryOverrides = array(), $factories = null)
    {
        if(is_null($factories)) {
            $factories = new Config\DefaultFactories();
        }
        
        $testDbAcle = new TestDbAcle();
        
        $serviceLocator = new ServiceLocator($factories->getFactories($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)));
        $serviceLocator->addFactories($factoryOverrides);
        $serviceLocator->setService('pdo', $pdo);
        
        $testDbAcle->setServiceLocator($serviceLocator);
        
        return $testDbAcle;
    }
    
    
}
