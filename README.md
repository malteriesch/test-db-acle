[![Build Status](https://travis-ci.org/malteriesch/test-db-acle.png?branch=master)](https://travis-ci.org/malteriesch/test-db-acle)

Test-DB-Acle
============

Pronounced test debacle... I guess you can leave out the -db- part if you want a slightly shorter word and feel so inclined.

What is Test-DB-Acle? 
------------------------------------------------

Test-DB-Acle is a PHP library to facilitate writing easy and concise tests for the database layer. 

It is my belief that writing tests should be as easy as possible for the developer, and they should also be as easy to read as possible for other developers to pick up.

This means any test data in the test should be relevant to the test scenario only. Most database tables however have non-null columns that require you to enter dummy data that is just introducing cognitive noise  to the test. 

Test-Db-Acle aims to take this burden away from the developer. The testing framework does not make any assumptions on how your database layer classes work, if they use an ORM like Doctrine, stored procedures or straight SQL, the principle is always the same, at some point the code interacts with the data in the DB tables, and we do need to test this.

Features
-------------------
* Installation via composer
* disables foreign key checks in database 
* automatically deals with non-null columns
* automatically trims date/time columns in mysql when asserting values are in db tables - useful for inserted timestamps (in Sqlite these columns have to be specified)
* pretty much all components can be exchanged and replaced by custom varieties
* supports Mysql and Sqlite at present
* Is framework agnostic, it makes no assumption if ORMs are used for example and should be easily adaptable for other unit test frameworks.

-------------------

Supported databases are, at present, MySql (and by extension MariaDB) and Sqlite.

The architecture should allow further databases to be added.

Supported Test Frameworks
-------------------------

This library can be used out-of-the-box with PHPUnit. It should be fairly easy to use it with other test frameworks by using the traits provided  and having the 'assertEquals' method delegate to the equivalent method (for example, I remember it being 'assertEqual' in SimpleTest)

I have provided a simple TestDbAcle\PhpUnit\AbstractTestCase as well as \TestDbAcle\PhpUnit\Traits\DatabaseHelperTrait if you are using PHP 5.4 and don't mind using traits.

Introduction and (very) quick example
-------------------------------------

Ok, to be really fair, testing the database layer is expensive and slooooows down tests, and wherever possible any dependencies on the database should be mocked. But sometimes we just have to do this, hopefully in a well-structured application this can be kept to a minimum.

There are many tools and approaches available (for example DBUnit or using factory methods in your unit tests) to help with database testing, each with their own strengths, this approach works for me because:
* I don't have to worry about null columns or foreign key constraints
* I provide a minimal data set for my tests
* I can see the test data in a grid-like format above the tests and again when asserting the data in the db.

So I set up my own solution.

###So how does it work and what does it look like? Show me an example!###

The idea is to use a "pipe-separated-values" text string, let's call it PSV - as in CSV, but with pipes - to set up test fixtures like this:

(The format of the PSV is very similar to the format used by the excellent Behat BDD framework (https://github.com/Behat/Behat))

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
//use this if you do not want to use the provided traits or abstract test cases
$testDbAcle = \TestDbAcle\TestDbAcle::create($pdo);
$testDbAcle->runCommand(new \TestDbAcle\Commands\SetupTablesCommand($dbTablesToSetup));
//use this if you extended your test case from \TestDbAcle\PhpUnit\AbstractTestCase or are using the \TestDbAcle\PhpUnit\Traits\DatabaseHelperTrait:
$this->setupTables($dbTablesToSetup);

```


The framework itself knows which columns are not NULL-able in the table and inserts default values... In fact, let's assume that the table *table_name* has 30 columns all of which are non-null....

Also, there might be various foreign key constraints going on in the background. Test-Db-Acle temporarily disables foreign key checks, so we do not have to worry about this, or in which order we insert the test data.

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
            ", array(), "Stuff works");
    }
}
```
Ok, obviously \Services\AddressService does not exist here (hey, it is test-first, right?) and the example is quite simple.

In real life, I would put the getPdo method into a common base test class for the project and it might be obtained quite differently than here. But, well, this *is* an example.

As you can see, setupTables can set up several tables at once and assertTableStateContains can verify the state of various tables at the same time, too.

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


Contributing
-------------------------------
Contributions (and criticisms) are more than welcome...!

###How to run the Test-Db-Acle tests###
To run the tests, you will need to create an empty database on a MySql sever of your choice, copy tests/Functional/config.php.dist to tests/Functional/config.php and populate with your database details.
Then, hopefully, all the tests should run. (The database is actually only needed for the functional smoke test)

More Documentation
--------------
- [PSV Syntax](docs/PsvParser.md)
- [AbstractTestCase](docs/AbstractTestCase.md)
- [Extending and customizing TestDbAcle](docs/Customizing.md)
- [Changelog](docs/Changelog.md)
