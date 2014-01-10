<?php
namespace TestDbAcle\Filter;

class PlaceholderRowFilter implements RowFilter {
    
    protected $replace;
    
    public function __construct(array $replace = array())
    {
        $this->replace = $replace;
    }
    
    public function filter($tableName, array $row)
    {
        foreach($row as $key => $value ){
            if (isset($this->replace[$value])){
                $row[$key] = $this->replace[$value];
            }
        }
        return $row;
    }
    
}

