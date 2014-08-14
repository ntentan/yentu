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
                $details['name'], self::convertTypes($details['type'], 
                    \yentu\descriptors\Postgresql::convertTypes(
                        $details['type'], 
                        \yentu\descriptors\Postgresql::TO_POSTGRESQL
                    )
                )
            )
        );
    }
    
    public function addPrimaryKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s PRIMARY KEY (%s)',
                $this->buildTableName($details['table'], $details['schema']),
                $details['name'] == '' ? "{$details['table']}_pk" : $details['name'],
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
    
    public function addAutoPrimaryKey($details) 
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

    public function addForeignKey($details) 
    {
        $name = $details['name'] == '' 
            ? 
            $details['table'] . '_' . implode('_', $details['columns']) . 
            '_' . $details['foreign_table'] . 
            '_'. implode($details['foreign_columns']) . '_fk' 
            : $details['name'];
        
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
    
    public function doesSchemaExist($name) 
    {
        $schema = $this->query(
            "SELECT schema_name FROM information_schema.schemata WHERE schema_name = ?",
            array($name)
        );
        if(count($schema) > 0) return true; return false;
    }

    public function doesTableExist($details) 
    {
        
        if(is_string($details))
        {
            $details = array(
                'name' => $details,
                'schema' => false
            );
        }
        
        $tables = $this->query(
            "SELECT table_name FROM information_schema.tables WHERE table_name = ? and table_schema = ? and table_type = 'BASE TABLE'",
            array(
                $details['name'],
                $details['schema'] == '' ? 'public' : $details['schema']
            )
        );
        
        if(count($tables) > 0) return true; else return false;
    }

    public function doesColumnExist($details) 
    {
        $column = $this->query(
            "SELECT column_name FROM information_schema.columns where table_name = ? and table_schema = ? and column_name = ? ",
            array(
                $details['table'],
                $details['schema'],
                $details['name']
            )
        );
        if(count($column) > 0) return true; else return false;
    }
    
    public function doesForeignKeyExist($details)
    {
        $column = $this->query(
            "SELECT constraint_name FROM information_schema.key_column_usage "
                . "JOIN information_schema.table_constraints "
                . "USING (constraint_name) where constraint_type = 'FOREIGN KEY' "
                . "AND table_constraints.table_name = ? "
                . "AND table_constraints.table_schema = ? "
                . "AND column_name in "
                . "(?" . str_repeat(', ?', count($details['columns']) - 1) . ")",
            array_merge(
                array(
                    $details['table'],
                    $details['schema'],
                ),
                $details['columns']
            )
        );
        if(count($column) > 0) return $column[0]['constraint_name']; else false;
    }

    public function dropForeignKey($details) 
    {
        $this->query(
            sprintf(
                "ALTER TABLE %s DROP CONSTRAINT %s",
                $this->buildTableName($details['table'], $details['schema']),
                $details['name']
            )
        );
    }
}
