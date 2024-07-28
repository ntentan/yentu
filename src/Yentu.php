<?php
namespace yentu;

use yentu\factories\DatabaseItemFactory;
use yentu\database\ItemType;
use yentu\database\Schema;
use yentu\database\EncapsulatedStack;

class Yentu
{
    private static DatabaseItemFactory $factory;
    private static Schema $defaultSchema;
    private static EncapsulatedStack $stack;
    
    public static function setup(DatabaseItemFactory $factory, string $defaultSchema)
    {
        self::$defaultSchema = $factory->create(ItemType::Schema, $defaultSchema);
        self::$factory = $factory;
    }
    
    public static function setStack(EncapsulatedStack $stack)
    {
        self::$stack = $stack;
    }
    
    public static function begin() {
        return self::$factory->create(ItemType::Begin, self::$defaultSchema, self::$stack);
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

