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
    
    protected function getAttributeAsArray($name, $default = null)
    {
        $value = $this->getAttribute($name, $default);
        if(!is_array($value)){
            return array($value);
        }else {
            return $value;
        }
    }
    
    public function isReplaceMode()
    {
        return $this->getAttribute('mode') ==  'replace';
    }
    
    public function getIdentifyColumns()
    {
        return $this->getAttribute('identifiedBy', array());
    }
    
    public function getTruncateDateColumns()
    {
        return $this->getAttributeAsArray('truncateDates', array());
    }
    
   
}