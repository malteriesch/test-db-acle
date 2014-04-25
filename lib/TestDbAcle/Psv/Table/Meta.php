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
    
    protected function getAttribute($name, $default = null)
    {
        if (isset($this->meta[$name])){
            return $this->meta[$name];
        }
        return $default;
    }
    
    public function isReplaceMode()
    {
        return $this->getAttribute('mode') ==  'replace';
    }
    
    public function getIdentifyColumns()
    {
        return $this->getAttribute('identifiedBy', array());
    }
    
   
}