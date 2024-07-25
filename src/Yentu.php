<?php
namespace yentu;

use yentu\factories\DatabaseItemFactory;
use yentu\database\ItemType;
use yentu\database\Schema;

class Yentu
{
    private static DatabaseItemFactory $factory;
    private static Schema $defaultSchema;
    
    public static function setup(DatabaseItemFactory $factory, string $defaultSchema)
    {
        self::$defaultSchema = $factory->create(ItemType::Schema, $defaultSchema);
        self::$factory = $factory;
    }
    
    public static function begin() {
        return self::$factory->create(ItemType::Begin, self::$defaultSchema);
    }

    public static function refschema($name) {
        $schema = self::$factory->create(ItemType::Schema, $name);
        $schema->setIsReference(true);
        return $schema;
    }

    public static function reftable($name) {
        $table = self::$factory->create(ItemType::Table, $name, self::$defaultSchema);
        $table->setIsReference(true);
        return $table;
    }
}

