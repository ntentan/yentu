<?php
namespace yentu;

use yentu\commands\Migrate;
use yentu\database\Table;
use yentu\database\Schema;  

/**
 * Utility class for yentu related functions.
 */
class Yentu
{
    private static Migrate $migrateCommand;
    
    public static function setMigrateCommand(Migrate $migrateCommand)
    {
        self::$migrateCommand = $migrateCommand;
    }
    
    public static function begin() {
        return self::$migrateCommand->getBegin();
    }

    public static function refschema($name) {
        $schema = new yentu\database\Schema($name);
        $schema->setIsReference(true);
        return $schema;
    }

    public static function reftable($name) {
        $table = new Table($name, new Schema(self::$migrateCommand->getDefaultSchema()));
        $table->setIsReference(true);
        return $table;
    }
}
