Psv Parser Syntax
=================

###Basic syntax independent of tables###

This syntax is use in used for example in *TestDbAcle\PhpUnit\AbstractTestCase::assertTableStateContains*, but also underneath any table headers in *TestDbAcle\PhpUnit\AbstractTestCase::setupTables* (see below)

The basic syntax is quite simple:
columns are seperated by pipes and the first line is the sql database column names. columns prefixed with a # get ignored.

        
    id  |first_name   |last_name |#comments
    10  |NULL         |Meyer     |no comment    
    20  |stu          |Smith     |something interesting

Translates into this array::
```php
    array(
        array("id" => "10",
            "first_name" => null,
            "last_name" => "Meyer",
        array("id" => "20",
            "first_name" => "stu",
            "last_name" => "Smith"),
    );
```

the following  characters need to be escaped with a backslash (\):
```    
    |[]\#
```

So

    id  |first_name   |last_name       
    10  |NULL         |\[\|\#miller\\\]
    20  |stu          |Smith     
    
    #this is an example
    
yields:

```php
    array(
        array("id" => "10",
            "first_name" => null,
            "last_name" => "[|#miller\]",
        array("id" => "20",
            "first_name" => "stu",
            "last_name" => "Smith"),
    );
```

It is possible to use the # inside columns as well, anything after gets ignored, also comments can be standalone in a line:

    id  |first_name   |last_name       
    10  |NULL         |miller
    20  |stu #a name  |Smith    
    
    #this is a comment line

translates to 
```php
    array(
        array("id" => "10",
            "first_name" => null,
            "last_name" => "miller",
        array("id" => "20",
            "first_name" => "stu",
            "last_name" => "Smith"),
    );
```

Whilst it is advisable for readability to line columns up, it is not required for the parser:

    id  |first_name   |last_name       
      10  |NULL    |miller
    20  |stu                  |Smith   
    
Is equivalent to the above.

###With table names and expressions###
This is used in: *TestDbAcle\PhpUnit\AbstractTestCase::setupTables*

    [user]
    id  |first_name   |last_name
    10  |john         |miller
    #----------------------------------
    #these 3 lines are igored
    #----------------------------------
    20  |stu          |Smith
        
Will insert the appropriate values into the user table.

By default the table gets cleared out beforehand.
It is possible to instead to replace rows:

    [user|mode:replace;identifiedBy:first_name,last_name]
    id  |first_name   |last_name    |some_column
    10  |john         |miller       |value 1
    #----------------------------------
    #these 3 lines are igored
    #----------------------------------
    20  |stu          |Smith        |value 2

This will not clear the user table but replace any rows with *first_name=john* and *last_name=miller* with the respective specified row.
