AbstractTestCase
=================

A class inheriting from *TestDbAcle\PhpUnit\AbstractTestCase* needs to implement:
    abstract public function getPdo();

 
Then following methods are available:
```php
    protected function setAutoIncrement($table, $nextIncrement)

    protected function setupTables($psvContent, $replace = array());
    
    protected function assertTableStateContains( $expectedPsv, $message = '' );
```


##setAutoIncrement##
sets the next auto increment to the specified value

##setupTables##
sets up a database fixture.
It is possible to optionally provide a replacement array:
```php
$this->setupTables("
            [address]
            address_id  |company
            1           |me
            3           |PLACEHOLDER

            [user]
            user_id |name
            1       |mary

        ",array('PLACEHOLDER'=>'John'));
```

will set the address.company column to John where the id=3. This is useful if you want to insert multiline values, or to be descriptive:

```php
$this->setupTables("
            [address]
            address_id  |company        |date_of_entry
            1           |me             |2001-01-01
            3           |John           |TODAY

            [user]
            user_id |name
            1       |mary

        ",array('TODAY'=>date("Y-m-d"));
```






