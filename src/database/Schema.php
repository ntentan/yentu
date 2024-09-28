<?php
namespace yentu\database;


class Schema extends DatabaseItem implements Changeable, Initializable
{
    private string $name;
    private bool $isReference;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    #[\Override]
    public function initialize(): void
    {
        if(!$this->getChangeLogger()->doesSchemaExist($this->name)) {
            $this->getChangeLogger()->addSchema($this->name);
        }   
    }
    
    public function isReference(): bool
    {
        return $this->isReference;
    }
    
    public function setIsReference(bool $isReference): void
    {
        $this->isReference = $isReference;
    }
    
    public function table($name): Table
    {
        if($this->isReference) {
            $table = new Table($name, $this);
        } else {
            $table = $this->create('table', $name, $this);
        }
        $table->setIsReference($this->isReference);
        return $table;
    }
    
    public function view($name)
    {
        return $this->create('view', $name, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }

    #[\Override]
    public function buildDescription() {
        return array(
            'name' => $this->name
        );
    }
}

