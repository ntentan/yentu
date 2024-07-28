<?php
namespace yentu\database;

use yentu\database\ItemType;


class Table extends DatabaseItem implements Changeable
{
    private Begin|Schema $schema;
    private array $primaryKeyColumns;
    private bool $isReference;    
    public string $name;
    
    public function __construct(string $name,  Begin|Schema $schema) 
    {
        $this->name = $name;
        $this->schema = $schema;
    }
    
    #[\Override]
    public function init()
    {
        if(!$this->getDriver()->doesTableExist($this->buildDescription())) {
            $this->getDriver()->addTable($this->buildDescription());
            $this->new = true;
        }        
    }
    
    public function setIsReference($isReference)
    {
        $this->isReference = $isReference;
    }
    
    public function isReference()
    {
        return $this->isReference;
    }
    
    public function column($name)
    {
        return $this->factory->create(ItemType::Column, $name, $this);
    }
    
    public function drop()
    {
        $table = $this->getDriver()->getDescription()->getTable(
            array(
                'table' => $this->name,
                'schema' => $this->schema->getName()
            )
        );
        $this->getDriver()->dropTable($table);
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSchema()
    {
        return $this->schema;
    }
    
    public function primaryKey(string ...$args)
    {
        $this->primaryKeyColumns = $args;
        return $this->factory->create(ItemType::PrimaryKey, $args, $this);
    }
    
    public function index()
    {
        return $this->create('index', func_get_args(), $this);
    }
    
    public function unique()
    {
        return $this->create('unique_key', func_get_args(), $this);
    }
        
    public function foreignKey(string ... $args)
    {
        return $this->factory->create(ItemType::ForeignKey, $args, $this);
    }
    
    public function table($name)
    {
        return $this->factory->create(ItemType::Table, $name, $this->schema);
    }
    
    public function insert(array $columns, array $items)
    {
        $driver = $this->getDriver();
        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            "{$driver->quoteIdentifier($this->schema->getName())}.{$driver->quoteIdentifier($this->name)}",
            implode(", ", array_map(fn($x) => $driver->quoteIdentifier($x), $columns)),
            implode(", ", array_fill(0, count($items[0]), "?"))                             
        );
        
        foreach($items as $row) {
            $this->getDriver()->query($query, $row);
        }
        
        return $this;
    }
    
    public function view($name)
    {
        return $this->create('view', $name, $this->schema);
    }

    #[\Override]
    public function buildDescription() {
        return array(
            'name' => $this->name,
            'schema' => $this->schema->getName()
        );
    }
}
