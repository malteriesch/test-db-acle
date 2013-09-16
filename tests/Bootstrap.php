<?

error_reporting(E_ALL);

$autoload = realpath(__DIR__.'/../vendor/autoload.php');
if (!file_exists($autoload)) {
    throw new Exception(
        'Please run "php composer.phar install" in root directory '
        . 'to setup unit test dependencies before running the tests'
    );
}

require $autoload;