<?php
namespace yentu\drivers;

class Postgresql extends Pdo
{
    
    private function buildTableName($name, $schema)
    {
        return ($schema === false ? '' : "\"{$schema}\".") . "\"$name\"";
    }

    protected function getDriverName() 
    {
        return 'pgsql';
    }

    public function addSchema($name) 
    {
        $this->query(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $name));
    }
    
    public function dropSchema($name) 
    {
        //$this->query()
    }

    public function addTable($details) 
    {
        $this->query(sprintf('CREATE TABLE IF NOT EXISTS %s ()',  $this->buildTableName($details['name'], $details['schema'])));        
    }

    public function dropTable($details) 
    {
        
    }

    public function describe() 
    {
        $descriptor = new \yentu\descriptors\Postgresql($this);
        return $descriptor->describe();
    }

    public function addColumn($details) 
    {
        $this->query(
            sprintf('ALTER TABLE %s ADD COlUMN %s %s', 
                $this->buildTableName($details['table'], $details['schema']),
                $details['name'], $this->convertTypes($details['type'])
            )
        );
    }
    
    public function addPrimaryKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD PRIMARY KEY (%s)',
                $this->buildTableName($details['table'], $details['schema']),
                implode(',', $details['columns'])
            )
        );
    }
    
    public function makeAutoPrimaryKey($details) 
    {
        $sequence = $this->buildTableName("{$details['table']}_{$details['column']}_seq", $details['schema']);
        $this->query("CREATE SEQUENCE $sequence");
        $this->query(
            sprintf(
                "ALTER TABLE %s ALTER COLUMN %s SET DEFAULT nextval('%s')",
                $this->buildTableName($details['table'], $details['schema']),
                $details['column'],
                $sequence
            )
        );
    }    
    
    public function setColumnNulls($details)
    {
        if($details['nulls'] === false)
        {
            $this->query(
                sprintf('ALTER TABLE %s ALTER COLUMN %s SET NOT NULL',
                    $this->buildTableName($details['table'], $details['schema']),
                    $details['name']
                )
            );
        }
        else
        {
            $this->query(
                sprintf('ALTER TABLE %s ALTER COLUMN %s DROP NOT NULL',
                    $this->buildTableName($details['table'], $details['schema']),
                    $details['name']
                )
            );            
        }
    }
    
    protected function convertTypes($type)
    {
        switch($type)
        {
            case 'integer':
                return 'integer';
                
            case 'string':
                return 'character varying';
                
            case 'double':
                return 'numeric';
                
            case 'timestamp':
                return 'timestamp with time zone';
                
            case 'text':
                return 'text';
                
            case 'boolean':
                return 'boolean';
                
            default:
                throw new \Exception("Unknown data type '$type'");
        }
    }
}
