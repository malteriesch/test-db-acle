<?php
namespace TestDbAcle;

class ServiceLocator 
{
    protected $factories = array();
    protected $services  = array();

    function __construct($factories = array())
    {
        $this->setFactories($factories);
    }
    
    function setFactories($factories)
    {
        $this->factories = $factories;
    }
    
    function addFactories($factories)
    {
        foreach($factories as $key=>$factory){
            $this->setFactory($key, $factory);
        }
    }
  
    function get($name)
    {
        if (!isset($this->services[$name])){
            $this->services[$name] = $this->createNew($name);
        }
        return $this->services[$name];
    }
    
    function createNew($name)
    {
        if (!isset($this->factories[$name])){
            return null;
        }

        $factory = $this->factories[$name];
        if (is_string($factory)){
            return new $factory();
        }else{
            return $factory($this);
        }
    }
    
    function setService($name, $service)
    {
        $this->services[$name] = $service;
    }
    
    protected function setFactory($name, $factory)
    {
        if (isset($this->factories[$name])){
            $this->setFactory("prototype.$name", $this->factories[$name]);
        }
        $this->factories[$name] = $factory;
    }
    
    
}
