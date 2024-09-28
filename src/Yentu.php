<?php
namespace yentu;

use yentu\database\Begin;
use yentu\database\Schema;
use yentu\database\Table;
use yentu\factories\DatabaseItemFactory;
use yentu\database\ItemType;


class Yentu
{
    private static DatabaseItemFactory $factory;
    private static Schema $defaultSchema;
    
    public static function setup(DatabaseItemFactory $factory, string $defaultSchema): void
    {
        self::$defaultSchema = $factory->getDefaultSchema($defaultSchema);
        self::$factory = $factory;
    }
    
    public static function begin(): Begin
    {
        /** @var Begin $begin */
        $begin = self::$factory->create(ItemType::Begin, self::$defaultSchema);
        $begin->setStack(self::$factory->getEncapsulatedStack());
        return $begin;
    }

    public static function refschema($name) {
        /** @var Schema $schema */
        $schema = self::$factory->create(ItemType::Schema, $name);
        $schema->setIsReference(true);
        return $schema;
    }

    public static function reftable($name) {
        /** @var Table $table */
        $table = self::$factory->create(ItemType::Table, $name, self::$defaultSchema);
        $table->setIsReference(true);
        return $table;
    }
}

