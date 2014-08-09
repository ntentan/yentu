<?php
namespace yentu\descriptors;

class Postgresql extends \yentu\SchemaDescriptor
{
    /**
     * 
     * @note Query sourced from http://stackoverflow.com/questions/2204058/show-which-columns-an-index-is-on-in-postgresql
     * @param type $table
     * @return type
     */
    protected function getIndices($table)
    {
        $constraints = array();        
        $constraintColumns = $this->driver->query(
            sprintf("select
                        t.relname as table_name,
                        i.relname as index_name,
                        a.attname as column_name
                    from
                        pg_class t,
                        pg_class i,
                        pg_index ix,
                        pg_attribute a,
                        pg_namespace n
                    where
                        t.oid = ix.indrelid
                        and i.oid = ix.indexrelid
                        and a.attrelid = t.oid
                        and a.attnum = ANY(ix.indkey)
                        and t.relkind = 'r'
                        and t.relname = '%s'
                        and n.nspname = '%s'
                        and i.relnamespace = n.oid
                            AND indisunique != 't'
                            AND indisprimary != 't'", 
            $table['name'], $table['schema'])
        );
        foreach($constraintColumns as $column)
        {
            $constraints[$column['constraint_name']][] = $column['column_name'];
        }
        
        return $constraints;        
    }
    
    protected function convertTypes($type)
    {
        switch($type)
        {
            case 'integer':
                return 'integer';
                
            case 'character varying':
                return 'string';
                
            case 'numeric':
                return 'double';
                
            case 'timestamp without time zone':
                return 'timestamp';
                
            case 'text':
                return 'text';
                
            case 'boolean':
                return 'boolean';
                
            default:
                throw new \Exception("Unknown data type '$type'");
        }
    }
    
    protected function getColumns($table)
    {
        $columns = array();
        $columnDetails = $this->driver->query(
            sprintf(
                "select column_name as name, data_type as type, is_nullable as nulls, column_default as default
                from information_schema.columns
                where table_name = '%s' and table_schema='%s'", 
                $table['name'], $table['schema']
            )
        );
        
        foreach($columnDetails as $i => $column)
        {
            $columns[$column['name']] = $column;
            $columns[$column['name']]['type'] = $this->convertTypes($columnDetails[$i]['type']);
        }
        
        return $columns;
    }
    
    /**
     * @note Query sourced from http://stackoverflow.com/questions/1152260/postgres-sql-to-list-table-foreign-keys
     * @param type $table
     */
    protected function getForeignConstraints($table)
    {
        $constraints = array();        
        $constraintColumns = $this->driver->query(
            sprintf("SELECT
                        tc.constraint_name, kcu.column_name, 
                        ccu.table_name AS foreign_table_name,
                        ccu.table_schema AS foreign_schema_name,
                        ccu.column_name AS foreign_column_name 
                    FROM 
                        information_schema.table_constraints AS tc 
                        JOIN information_schema.key_column_usage AS kcu
                          ON tc.constraint_name = kcu.constraint_name
                        JOIN information_schema.constraint_column_usage AS ccu
                          ON ccu.constraint_name = tc.constraint_name
                    WHERE constraint_type = 'FOREIGN KEY' 
                        AND tc.table_name='%s' AND tc.table_schema='%s'",
                $table['name'], $table['schema']
            )
        );  
        
        foreach($constraintColumns as $column)
        {
            $constraints[$column['constraint_name']][] = $column['column_name'];
        }
        
        return $constraints;
    }
    
    protected function getConstraint($table, $type)
    {
        $constraints = array();
        $constraintColumns = $this->driver->query(
            sprintf("select column_name, pk.constraint_name from 
                information_schema.table_constraints pk 
                join information_schema.key_column_usage c on 
                   c.table_name = pk.table_name and 
                   c.constraint_name = pk.constraint_name and
                   c.constraint_schema = pk.table_schema
                where pk.table_name = '%s' and pk.table_schema='%s'
                and constraint_type = '%s'",
                $table['name'], $table['schema'], $type
            )
        );
        
        foreach($constraintColumns as $column)
        {
            $constraints[$column['constraint_name']][] = $column['column_name'];
        }
        
        return $constraints;
    }
    
    protected function getTables($schema)
    {
        $description = array();
        $tables = $this->driver->query(
            "select table_schema as schema, table_name as name 
            from information_schema.tables
            where table_schema = '$schema'"
        );
        
        foreach($tables as $table)
        {
            $table['columns'] = $this->getColumns($table);
            $table['primary_key'] = $this->getConstraint($table, 'PRIMARY KEY');
            $table['unique_keys'] = $this->getConstraint($table, 'UNIQUE');
            $table['foreign_keys'] = $this->getForeignConstraints($table);
            $table['indices'] = $this->getIndices($table);
            
            $primaryKey = reset($table['primary_key']);
            if(count($primaryKey) == 1 && substr_count($table['columns'][$primaryKey[0]]['default'], 'nextval'))
            {
                $table['auto_increment'] = true;
                unset($table['columns'][$primaryKey[0]]['default']);
            }
            
            $description[$table['name']] = $table;
        }        
        
        return $description;
    }

    public function describe() 
    {
        $description = array(
            'schemata' => array(),
        );
        
        $description['schemata'] = $this->driver->query(
            "select schema_name as name from information_schema.schemata 
            where schema_name not like 'pg_temp%' and 
            schema_name not like 'pg_toast%' and 
            schema_name not in ('pg_catalog', 'information_schema')"
        );
        
        foreach($description['schemata'] as $i => $schema)
        {
            $description['schemata'][$i]['tables'] = $this->getTables($schema['name']);
            if($schema['name'] == 'public' && count($description['schemata'][$i]['tables'] == 0))
            {
                unset($description['schemata'][$i]);
            }
        }
        
        return $description;  
    }
}

