Test-DB-Acle
============

(pronounced test -debacle, I also use the short form test-acle)

A PHP library to facilitate easy and concise tests for the database layer, initially 
for MySql, although I can see Oracle et al support possible.... 

This library can be used with PHPUnit as well as Simpletest...

Ok, testing the database layer is expensive, and wherever possible any dependencies on the database should be mocked but sometimes we just have to do it. I was frustrated with the existing tools available (dependencies on particular XUnit frameworks, not being able to use them together with different test base classes and way too verbose), so I set up my own.

The idea is to use a "pipe-separated-values" text string to set up test fixtures like this:
```php
$dbTablesToSetup="

[table_name]
id  |date        |name    |value  |dependent_table_id
10  |2001-01-01  |foo     |900    |60


[dependent_table]
id |name
20 |Bar
60 |Baz

";
```

Where the framework itself knows which columns are not NULL-able in the table and inserts default values...


Very, brief, instructions:
unzip lib/TestDbAcle into somewhere and enable autoload for it (for example, by using composer), it follows the PSR convention

in your test case, assuming you have an instance of a PDO class in ```$pdo```, you can use this to have the data inserted into your database:
```php
$dbTablesToSetup="

[table_name]
id  |date        |name    |value  |dependent_table_id
10  |2001-01-01  |foo     |900    |60


[dependent_table]
id |name
20 |Bar
60 |Baz

";

$dataInserter = new \TestDbAcle\Db\DataInserter($pdo);
$psvParser = new \TestDbAcle\Psv\PsvParser();


$dataInserter->process($psvParser->parsePsvTree($dbTablesToSetup));
```