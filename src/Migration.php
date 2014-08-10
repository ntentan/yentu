<?php
namespace yentu;

class Migration
{
    public function init()
    {
        // Get the version of the current database so we know which migrations
        // to run. In cases where a version table doesn't exist and no tables
        // exist in the target database, create a new version table and run
        // all migrations to the required target.
        
    }
    
    public function run()
    {
        database\DatabaseItem::setDriver(DatabaseDriver::getConnection());
        require 'yentu/migrations/seed.php';
    }
    
    public function schema($schemaName)
    {
        return new database\Schema($schemaName);
    }
}
