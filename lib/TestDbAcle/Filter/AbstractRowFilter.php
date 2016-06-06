<?php
namespace TestDbAcle\Filter;
use TestDbAcle\Psv\Table\Table;

abstract class  AbstractRowFilter implements RowFilter
{

    public function skip(Table $table)
    {
        return false;
    }
}