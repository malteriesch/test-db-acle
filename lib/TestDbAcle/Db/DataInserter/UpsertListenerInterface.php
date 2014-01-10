<?php
namespace TestDbAcle\Db\DataInserter;

interface UpsertListenerInterface
{
    public function afterUpsert(Sql\UpsertBuilder $upsertBuilder);
}

