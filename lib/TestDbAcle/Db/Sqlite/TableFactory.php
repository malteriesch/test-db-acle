<?php
namespace TestDbAcle\Db\Sqlite;
class TableFactory extends \TestDbAcle\Db\AbstractTableFactory
{

    public function createTable($name){
        return new \TestDbAcle\Db\Table\Table($name);
    }
    
    public function createColumn(array $description){
        return new \TestDbAcle\Db\Sqlite\Table\Column($description);
    }
    
    
}