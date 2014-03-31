<?php
namespace TestDbAcle\Psv\Table;

class Meta
{
    protected $meta;
    
    function __construct(array $meta = array())
    {
        $this->meta = $meta;
    }
    function toArray()
    {
        return $this->meta;
    }
   
}