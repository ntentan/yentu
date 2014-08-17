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
                $details['name'], 
                    \yentu\descriptors\Postgresql::convertTypes($details['type'], 
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
    
    public function addUniqueKey($details)
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
    
    private function dropKeyItem($details, $type)
    {
        $this->query(
            sprintf(
                "ALTER TABLE %s DROP CONSTRAINT %s",
                $this->buildTableName($details['table'], $details['schema']),
                $details['name']
            )
        );
        
        $this->dropItem($details, $type);
    }
    
    public function dropUniqueKey($details) 
    {
        $this->dropKeyItem($details, 'unique_keys');
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
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) MATCH FULL ON DELETE %s ON UPDATE %s',
                $this->buildTableName($details['table'], $details['schema']),
                $details['name'], 
                implode(',', $details['columns']), 
                $this->buildTableName($details['foreign_table'], $details['foreign_schema']),
                implode(',', $details['foreign_columns']),
                $details['on_delete'] == '' ? 'NO ACTION' : $details['on_delete'],
                $details['on_update'] == '' ? 'NO ACTION' : $details['on_update']
            )
        );
    }

    public function dropForeignKey($details) 
    {
        $this->dropKeyItem($details, 'foreign_keys');
    }
}
