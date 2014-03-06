Test-DB-Acle
============

Pronounced test-debacle. I guess you can leave out the -db- part if you feel so inclined. Not me, obviously :) 

What is Test-DB-Acle? A short mission statement.
------------------------------------------------

Test-DB-Acle is a PHP library to facilitate writing easy and concise tests for the database layer. 

It is my belief that writing tests should be as easy as possible for the developer, and they should also be as easy to read as possible for another developer to pick up (Ah, so easy to give advice..!)

This means any test data in the test should be relevant to the test scenario only. Most database tables however have non-null columns that require you to enter dummy data that is just introducing cognitive noise  to the test. 

Test-Db-Acle aims to take this burden away from the developer. This testing framework does not make any assumptions on how your database layer classes work, if they use an ORM like Doctrine, stored procedures or straight SQL. 

The principle is always the same, at some point the code interacts with the data in the DB tables, and we do need to test this.


Supported Databases
-------------------

Supported databases are, at present, MySql and by extension MariaDB.
I can see Postgres, Sqllite or even Oracle support possible in the future.

Supported Test Frameworks
-------------------------

This library can be used with PHPUnit as well as other frameorks such as Simpletest (The one that started me on my TDD journey). 

I have provided a simple TestDbAcle\PhpUnit\AbstractTestCase, an appropriate Simpletest one would need to be created, but should be as simple as exchanging the parent class.

Introduction and (very) quick example
-------------------------------------

Ok, to be really fair, testing the database layer is expensive and slooooows down tests, and wherever possible any dependencies on the database should be mocked. But sometimes we just have to do this, hopefully in a well-structured application this can be kept to a minimum.

I was frustrated with the existing tools available. Many had dependencies on particular XUnit frameworks, or I was not  able to use them together with different test base classes as they were difficult to integrate, and all of them were waaaay to verbose and cumbersome to use. 

So I set up my own solution.

###So how does it work and look like? Show me an example!###

The idea is to use a "pipe-separated-values" (imaginatively called PSV by me) text string to set up test fixtures like this:

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
$testDbAcle = \TestDbAcle\TestDbAcle::create($pdo);
$testDbAcle->setupTables(dbTablesToSetup);
```

Where the framework itself knows which columns are not NULL-able in the table and inserts default values... In fact, the table *table_name* has 30 columns all of which are non-null....

Also, there are various foreign key constraints going on in the background. Test-Db-Acle temporarily disables foreign key checks, so we do not have to worry about this, or in which order we insert the test data.

###What about an actual test.....?###

```php
class ExampleTest extends \TestDbAcle\PhpUnit\AbstractTestCase
{
    protected $pdo;
    protected $addressService;

    function Setup(){
        parent::Setup();
        $this->addressService = new \Services\AddressService();
    }

    function getPdo()
    {
        if (!isset($this->pdo)){
            $config = include(__DIR__."/config.php");
            $this->pdo = new \Pdo("mysql:dbname={$config['db_name']};host={$config['db_host']}",$config['db_user'],$config['db_password']);
        }
        return $this->pdo;
    }

    function test_AddressService()
    {
        $this->setupTables("
            [address]
            address_id  |company
            1           |me
            3           |you
            
            [user]
            user_id  |name
            10       |John
            20       |Mary
            
        ");
        
        $this->setAutoIncrement('address', 100);
        
        $this->addressService->addEntry("them");

        $this->assertTableStateContains("
            [address]
            address_id  |company
            1           |me
            3           |you
            100         |them
            
            [user]
            user_id  |name
            10       |John
            20       |Mary
            ","Stuff works");
    }
}
```
Ok, obviously \Services\AddressService does not exist here (Obviously, it is test-first, right?). The example is quite simple.

In real life, I would put the getPdo method into a common base test class for the project and it might be obtained quite differently than here. But, well, this *is* an example.

As you can see, setupTables can set up several tables at once and  assertTableStateContains can verify the state of various tables at the same time, too.

Similarly to how setupTables can setup tables that have many more columns than those specified, assertTableStateContains only compares and asserts the values of the columns specified too.

How To Install
--------------

The easiest way of installing Test-Db-Acle is by using composer ( Read more here: [http://packagist.org](http://packagist.org "Packagist") ), and I highly recommend using this approach,
although you can also unzip the package into a folder and enable autoload for it manually in whatever form you wish (it uses psr-0 naming convention)

To use with composer, add this to your composer.json file:

    "require": {
        "test-db-acle/test-db-acle" : "dev-master"
    },
    "repositories" : [
        {
          "type": "git",
          "url": "https://github.com/malteriesch/test-db-acle.git"
        }
    ]

You can also use https://packagist.org/packages/test-db-acle/test-db-acle if you prefer.

More Documentation
--------------
- [PSV Syntax](docs/PsvParser.md)
- [AbstractTestCase](docs/AbstractTestCase.md)
- [Extending and customizing TestDbAcle](docs/Customizing.md)