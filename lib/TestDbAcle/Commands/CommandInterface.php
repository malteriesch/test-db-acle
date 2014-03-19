<?php
namespace TestDbAcle\Commands;
interface CommandInterface
{
    function execute();
    function initialise(\TestDbAcle\ServiceLocator $serviceLocator);
}
