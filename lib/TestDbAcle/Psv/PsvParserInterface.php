<?php

namespace TestDbAcle\Psv;

interface PsvParserInterface 
{
    public function parsePsvTree($psvContent);
    public function parsePsv($psvTableContent);
}