<?php
namespace yentu\database;

class ForeignKey extends DatabaseItem
{
    private $table;
    private $columns;
    private $foreignTable;
    private $foreignColumns;
    private $name;
    
    public function __construct($columns, $table) 
    {
        $this->table = $table;
        $this->columns = $columns;
        
        // Prevent the committing of the foreign key even if the context
        // switches
        
        $constraint = $this->getDriver()->doesForeignKeyExist(array(
            'schema' => $table->getSchema()->getName(),
            'table' => $table->getName(),
            'columns' => $columns
        ));
        
        if($constraint === false)
        {
            DatabaseItem::disableCommitPending();
            $this->new = true;
        }
        else
        {
            $this->name = $constraint;
        }
    }
    
    public function references($table)
    {
        $this->foreignTable = $table;
        DatabaseItem::enableCommitPending();
        return $this;
    }
    
    public function columns()
    {
        $this->foreignColumns = func_get_args();
        return $this;
    }
    
    public function drop()
    {
        $description = $this->getDriver()->getDescription();
        $key = $description['schemata'][$this->table->getSchema()->getName()]['tables'][$this->table->getName()]['foreign_keys'][$this->name];
        
        $this->getDriver()->dropForeignKey(
            array(
                'columns' => $this->columns,
                'table' => $this->table->getName(),
                'schema' => $this->table->getSchema()->getName(),
                'foreign_columns' => $key['foreign_columns'],
                'foreign_table' => $key['foreign_table'],
                'foreign_schema' => $key['foreign_schema'],
                'name' => $this->name
            )
        );
        return $this;
    }

    public function commit() 
    {
        if($this->isNew())
        {
            $this->getDriver()->addForeignKey(
                array(
                    'columns' => $this->columns,
                    'table' => $this->table->getName(),
                    'schema' => $this->table->getSchema()->getName(),
                    'foreign_columns' => $this->foreignColumns,
                    'foreign_table' => $this->foreignTable->getName(),
                    'foreign_schema' => $this->foreignTable->getSchema()->getName(),
                    'name' => $this->name
                )
            );        
        }
    }
    
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

}