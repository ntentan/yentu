<?php
namespace yentu;

class Importer
{
    private $db;
    
    public function __construct()
    {
        $this->db = DatabaseDriver::getConnection();
    }
    
    public function run()
    {
        $schema = $this->db->describe();
        
    }
}
