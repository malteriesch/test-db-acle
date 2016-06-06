<?php

namespace TestDbAcle\Filter;
use TestDbAcle\Psv\Table\Table;

interface RowFilter{
    public function filter($tableName, array $row);
    public function skip(Table $table);
}