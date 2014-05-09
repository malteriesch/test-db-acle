<?php
namespace TestDbAcle\Db\Table;

abstract class AbstractColumn 
{
    protected $meta=array();
    
    function __construct(array $meta)
    {
        $this->meta = $meta;
    }
    
    abstract public function getName();
    
    abstract public function getDefault();
    
    abstract public function isAutoIncrement();
    
    abstract public function isDateTime();
    
    abstract public function isNullable();
    
    abstract public function generateDefaultNullValue();
    
    abstract public function isPrimaryKey();
}

