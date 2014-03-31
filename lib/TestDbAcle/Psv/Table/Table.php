<?php
namespace TestDbAcle\Psv\Table;

class Table
{
    protected $data;
    protected $meta;
    protected $name;
    
    function __construct($name, array $data=array(), Meta $meta = null)
    {
        if($meta == null){
            $meta= new Meta();
        }
        $this->name = $name;
        $this->data = $data;
        $this->meta = $meta;
    }
    
    function toArray()
    {
        return $this->data;
    }
    
    function setData(array $data)
    {
        $this->data = $data;
    }
    
    function getMeta()
    {
        return $this->meta;
    }
    
    function getName()
    {
        return $this->name;
    }
}