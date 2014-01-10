<?php
namespace TestDbAcle;
class ServiceLocator 
{
    protected $factories = array();
    protected $services  = array();

    function setFactories($factories)
    {
        $this->factories = $factories;
    }
    
    function get($name)
    {
        if (!isset($this->services[$name])){
            
            if (!isset($this->factories[$name])){
                return null;
            }
            
            $factory = $this->factories[$name];
            if (is_string($factory)){
                $this->services[$name] = new $factory();
            }else{
                $this->services[$name] = $factory($this);
            }
        }
        return $this->services[$name];
    }
    
    function set($name, $service)
    {
        if (isset($this->services[$name])){
            $this->services["prototype.$name"] = $this->services[$name];
        }
        $this->services[$name] = $service;
    }
}
