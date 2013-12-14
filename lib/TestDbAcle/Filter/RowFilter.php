<?php

namespace TestDbAcle\Filter;
interface RowFilter{
    public function filter($tableName, array $row);
}