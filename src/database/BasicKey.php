<?php
namespace yentu\database;

abstract class BasicKey extends DatabaseItem implements Commitable, Changeable //, Initializable
{
    protected array $columns = [];
    protected Table $table;
    protected ?string $name;
    
    public function __construct($columns, $table)
    {
        $this->columns = $columns;
        $this->table = $table;
    }

    #[\Override]
    public function isNew(): bool
    {
        if ($this->new === null) {
            $this->new = !$this->doesKeyExist($this->buildDescription());
        }
        return $this->new;
    }
    
    abstract protected function doesKeyExist($constraint);
    abstract protected function addKey($constraint);
    abstract protected function dropKey($constraint);
    abstract protected function getNamePostfix();
    
    #[\Override]
    public function buildDescription()
    {
        return array(
            'table' => $this->table->getName(), 
            'schema' => $this->table->getSchema()->getName(), 
            'columns' => $this->columns,
            'name' => $this->name
                ?? $this->table->getName() . '_' . implode('_', $this->columns) . '_' . $this->getNamePostfix()
        );
    }

    #[\Override]
    public function commitNew() 
    {
        $this->addKey($this->buildDescription());
    }
    
    public function drop()
    {
        if (!$this->isNew()) {
            $this->dropKey($this->buildDescription());
        }
        return $this;
    }
    
    public function name($name)
    {
        $this->name = $name;
        $this->new = !$this->doesKeyExist($this->buildDescription());
        return $this;
    }
}

