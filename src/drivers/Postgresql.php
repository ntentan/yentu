<?php
namespace yentu\drivers;

class Postgresql extends Pdo
{
    
    private function buildTableName($name, $schema)
    {
        return ($schema === false || $schema == ''? '' : "\"{$schema}\".") . "\"$name\"";
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
    
    public function addUniqueConstraint($details)
    {
        $name = "{$details['table']}_" . implode('_', $details['columns']) . "_uk";
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s UNIQUE (%s)',
                $this->buildTableName($details['table'], $details['schema']),
                $name,
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

    public function addForeignKey($details) 
    {
        $name = $details['table'] . '_' . implode('_', $details['columns']) . 
            '_' . $details['foreign_table'] . 
            '_'. implode($details['foreign_columns']) . '_fk';
        
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) MATCH FULL',
                $this->buildTableName($details['table'], $details['schema']),
                $name, 
                implode(',', $details['columns']), 
                $this->buildTableName($details['foreign_table'], $details['foreign_schema']),
                implode(',', $details['foreign_columns'])
            )
        );
    }

    public function doesTableExist($details) 
    {
        
        if(is_string($details))
        {
            $details = array(
                'table' => $details,
                'schema' => false
            );
        }
        
        $tables = $this->query(
            "SELECT * FROM information_schema.tables WHERE table_name = ? and table_schema = ?",
            array(
                $details['table'],
                $details['schema'] == '' ? 'public' : $details['schema']
            )
        );
        
        if(count($tables) > 0) return true; else return false;
    }
}
