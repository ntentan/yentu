<?php
namespace yentu;

class Migration
{
    private $driver;
    
    public function run()
    {
        $this->driver = DatabaseDriver::getConnection();
        require 'yentu/migrations/seed.php';
    }
    
    public function schema($schemaName)
    {
        return new database\Schema($schemaName, $this->driver);
    }
}
