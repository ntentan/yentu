<?php
namespace yentu\database;


class Table extends DatabaseItem implements Changeable, Initializable
{
    private Begin|Schema $schema;
    private bool $isReference;    
    public string $name;
    
    public function __construct(string $name,  Begin|Schema $schema)
    {
        $this->name = $name;
        $this->schema = $schema;
    }
    
    #[\Override]
    public function initialize(): void
    {
        if(!$this->getChangeLogger()->doesTableExist($this->buildDescription())) {
            $this->getChangeLogger()->addTable($this->buildDescription());
            $this->new = true;
        }        
    }
    
    public function setIsReference($isReference): void
    {
        $this->isReference = $isReference;
    }
    
    public function isReference(): bool
    {
        return $this->isReference;
    }
    
    public function column($name): Column
    {
        return $this->factory->create(ItemType::Column, $name, $this);
    }
    
    public function drop(): Table
    {
        $table = $this->getChangeLogger()->getDescription()->getTable(
            array(
                'table' => $this->name,
                'schema' => $this->schema->getName()
            )
        );
        $this->getChangeLogger()->dropTable($table);
        return $this;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getSchema(): Begin|Schema
    {
        return $this->schema;
    }
    
    public function primaryKey(string ...$args): PrimaryKey
    {
        return $this->factory->create(ItemType::PrimaryKey, $args, $this);
    }
    
    public function index(): Index
    {
        return $this->factory->create(ItemType::Index, func_get_args(), $this);
    }
    
    public function unique(): UniqueKey
    {
        return $this->factory->create(ItemType::UniqueKey, func_get_args(), $this);
    }
        
    public function foreignKey(string ... $args): ForeignKey
    {
        return $this->factory->create(ItemType::ForeignKey, $args, $this);
    }
    
    public function table($name): Table
    {
        return $this->factory->create(ItemType::Table, $name, $this->schema);
    }
    
    public function insert(array $columns, array $rows): Table
    {
        $this->getChangeLogger()->insertData(
            ['columns'=>$columns, 'rows'=>$rows, 'schema'=>$this->schema->getName(), 'table' => $this->name]
        );
        return $this;
    }
    
    public function view($name): View
    {
        return $this->factory->create(ItemType::View, $name, $this->schema);
    }

    #[\Override]
    public function buildDescription(): array
    {
        return array(
            'name' => $this->name,
            'schema' => $this->schema->getName()
        );
    }
}
