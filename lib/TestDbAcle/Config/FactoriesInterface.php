<?php
namespace TestDbAcle\Config;

interface FactoriesInterface  
{
    /**
     * @param string $pdoDriverName mysql|sqlite
     * @return array FactoriesInterface
     */
    public function getFactories($pdoDriverName);
}

