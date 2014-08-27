<?php
namespace yentu\drivers;

class Postgresql extends Pdo
{
    private function buildTableName($name, $schema)
    {
        return ($schema === false || $schema == '' ? '' : "\"{$schema}\".") . "\"$name\"";
    }

    protected function getDriverName() 
    {
        return 'pgsql';
    }

    protected function _addSchema($name) 
    {
        $this->query(sprintf('CREATE SCHEMA "%s"', $name));
    }
    
    protected function _dropSchema($name) 
    {
        $this->query(sprintf('DROP SCHEMA "%s"', $name));
    }

    protected function _addTable($details) 
    {
        $this->query(sprintf('CREATE TABLE  %s ()',  $this->buildTableName($details['name'], $details['schema'])));        
    }

    protected function _dropTable($details) 
    {
        $this->query(sprintf('DROP TABLE %s', $this->buildTableName($details['name'], $details['schema'])));
    }

    public function describe() 
    {
        $descriptor = new \yentu\descriptors\Postgresql($this);
        return $descriptor->describe();
    }

    protected function _addColumn($details) 
    {
        $this->query(
            sprintf('ALTER TABLE %s ADD COlUMN %s %s', 
                $this->buildTableName($details['table'], $details['schema']),
                $details['name'], 
                    \yentu\descriptors\Postgresql::convertTypes(
                        $details['type'], 
                        \yentu\descriptors\Postgresql::TO_POSTGRESQL
                    )
                )
            );
        $this->_changeColumnNulls($details);
        
    }
    
    protected function _dropColumn($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s DROP COLUMN %s', 
                $this->buildTableName($details['table'], $details['schema']),
                $details['name']
            )
        );
    }
    
    protected function _addPrimaryKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s PRIMARY KEY (%s)',
                $this->buildTableName($details['table'], $details['schema']),
                $details['name'],
                implode(',', $details['columns'])
            )
        );
    }
    
    protected function _dropPrimaryKey($details) 
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s DROP CONSTRAINT %s',
                $this->buildTableName($details['table'], $details['schema']),
                $details['name']
            )
        );
    }
    
    protected function _addUniqueKey($details)
    {
        $this->query(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s UNIQUE (%s)',
                $this->buildTableName($details['table'], $details['schema']),
                $details['name'],
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
    
    protected function _dropUniqueKey($details) 
    {
        $this->dropKeyItem($details, 'unique_keys');
    }    
    
    protected function _addAutoPrimaryKey($details) 
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
    
    protected function _dropAutoPrimaryKey($details) 
    {
        $sequence = $this->buildTableName("{$details['table']}_{$details['column']}_seq", $details['schema']);
        $this->query(
            sprintf(
                "ALTER TABLE %s ALTER COLUMN %s SET DEFAULT NULL",
                $this->buildTableName($details['table'], $details['schema']),
                $details['column'],
                $sequence
            )
        );
        $this->query("DROP SEQUENCE $sequence");
    }      
    
    /**
     * 
     * @param array $details
     */
    protected function _changeColumnNulls($details)
    {
        if($details['to']['nulls'] === false || $details['nulls'] === false)
        {
            // Remove the to key to make it possible for this function to be run
            // bu the _addColumn method
            if(isset($details['to']))
            {
                $details = $details['to'];
            }
            
            $this->query(
                sprintf('ALTER TABLE %s ALTER COLUMN %s SET NOT NULL',
                    $this->buildTableName($details['table'], $details['schema']),
                    $details['name']
                )
            );
        }
        else if(isset($details['to']))
        {
            $this->query(
                sprintf('ALTER TABLE %s ALTER COLUMN %s DROP NOT NULL',
                    $this->buildTableName($details['to']['table'], $details['to']['schema']),
                    $details['to']['name']
                )
            );            
        }
    }

    protected function _addForeignKey($details) 
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

    protected function _dropForeignKey($details) 
    {
        $this->dropKeyItem($details, 'foreign_keys');
    }

    protected function _addIndex($details) 
    {
        $this->query(
            sprintf(
                'CREATE INDEX %s %s ON %s (%s)',
                $details['unique'] ? 'UNIQUE' : '',
                $details['name'],
                $this->buildTableName($details['table'], $details['schema']),
                implode(', ', $details['columns'])
            )
        );
    }
    
    

    protected function _dropIndex($details) 
    {
        $this->query(sprintf('DROP INDEX %s', $this->buildTableName($details['name'], $details['schema'])));
    }

}
